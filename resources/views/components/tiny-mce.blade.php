<div class="mt-2" wire:ignore>
    <textarea {{ $attributes }}>{{ $slot }}</textarea>
    <script>   
      tinymce.init({
          selector: 'textarea',
          plugins: 'lists advlist',
          toolbar: 'undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | removeformat', 
          setup: function (editor) {
              editor.on('init change', function () {
                  editor.save();
              });
              editor.on('change', function (e) {
                  @this.set('{{ $content }}', editor.getContent());
              });
          }
      });
    </script>
</div>
