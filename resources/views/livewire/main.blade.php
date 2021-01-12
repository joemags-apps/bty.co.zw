<div>
    <form wire:submit.prevent="shortenLink">
        <div class="form-row">
            <div class="col-12 col-md-9 mb-2 mb-md-0">
                <input wire:model.defer="url" type="url" class="form-control form-control-lg"
                    placeholder="https://example.com" required>

            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-block btn-lg btn-primary">Shorten</button>
            </div>
        </div>
        @if (is_array($last))

            <div class="row mt-3">
                <div class="col-12">
                    <div class="refferal-info w-100">
                        <span class="refferal-copy-feedback copy-feedback"></span>
                        <em class="fas fa-link"></em>
                        <input type="text" class="refferal-address" value="{{$last['short']}}" disabled="">
                        <button type="button" class="refferal-copy copy-clipboard"
                            data-clipboard-text="{{$last['short']}}">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </form>
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.6/dist/clipboard.min.js"></script>

    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHAV3_SITEKEY') }}"></script>
    <script>
        document.addEventListener('livewire:load', function() {
            grecaptcha.ready(function() {
                grecaptcha.execute("{{ env('RECAPTCHAV3_SITEKEY') }}", {
                    action: 'main'
                }).then((token) => {
                    @this.token = token;
                });
            });

            // window.livewire.hook('element.updated', () => {
            //     console.log("Updated");
            //     // $('#select2ID').select2();
            // });
            function clip() {
                console.log("Clipping");
                var clipboard = new ClipboardJS('.copy-clipboard');
                clipboard.on('success', function(e) {
                    feedback(e.trigger, 'success');
                    e.clearSelection();
                }).on('error', function(e) {
                    feedback(e.trigger, 'fail');
                });

                // Copyto clipboard In Modal
                var clipboardModal = new ClipboardJS('.copy-clipboard-modal', {
                    container: document.querySelector('.modal')
                });
                clipboardModal.on('success', function(e) {
                    feedback(e.trigger, 'success');
                    e.clearSelection();
                }).on('error', function(e) {
                    feedback(e.trigger, 'fail');
                });
            }
            Livewire.on('already_short', function(){
                toastr.warning('Link already shortened');
            });

            function feedback(el, state) {
                if (state === 'success') {
                    $(el).parent().find('.copy-feedback').text('Link copied to clipboard').fadeIn().delay(1000)
                        .fadeOut();
                } else {
                    $(el).parent().find('.copy-feedback').text('Faild to Copy').fadeIn().delay(1000).fadeOut();
                }
            }

            clip();

        })

    </script>
</div>
