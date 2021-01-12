<?php

namespace App\Http\Livewire;

use App\Models\Short;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Lunaweb\RecaptchaV3\Facades\RecaptchaV3;
use Illuminate\Support\Str;

class Main extends Component
{
    public $url;
    public $shortened = false;

    public $human;

    public $token;

    public $links = [];

    public $last;

    public function updatedToken()
    {
        $score = RecaptchaV3::verify($this->token, 'main');
        if (!$score || $score < 0.1) {
            $this->addError('human', 'Browser validation error. Refresh page!');
            return;
        }
        $this->human = true;
    }

    public $rules = [
        'url' => 'required|url'
    ];

    public function render()
    {
        return view('livewire.main')
            ->extends('layouts.app');
    }

    public function mount(){
        $links = session('links');
        if(is_array($links)){
            $this->links = session('links');
            $this->last = collect(session('links'))->last();
        }else{
            session(['links' => []]);
        }
    }

    public function randomId($len = 6){

        $uid = Str::random($len);
        $validator = Validator::make(['uid'=>$uid],['uid'=>'unique:shorts,uid']);
        if($validator->fails()){
             return $this->randomId();
        }
        return $uid;
   }

    public function shortenLink(){

        if(!$this->human) return $this->redirect(url('/'));

        $this->validate();

        $shortened = Str::contains($this->url, route('main'));

        if($shortened){
            return $this->emit('already_short');
        }

        $code = $this->randomId();

        $link = Short::firstOrCreate([
            'url' => $this->url
        ], [
            'code' => $code
        ]);
        $ses = [
            'short' => $link->short,
            'redirect' => $link->url
        ];
        $found = false;
        foreach (session('links') as $val) {
            if(isset($val['short']) && $val['short'] == $link->short){
                $found = true;
            }
        }
        if(!$found){
            session()->push('links', $ses);
        }
        $this->links = session('links');
        $this->last = collect(session('links'))->last();
        $this->emit('links_updated');
    }
}
