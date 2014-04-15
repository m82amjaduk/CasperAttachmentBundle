if(window.jQuery) {
    (function($) {
        $.fn.formImagesCollection = function(options) {
            var container = $(this);
            var settings = $.extend({
                addBtn: '.add-image-btn',
                removeBtn: '.remove-image-btn',
                isPrimaryGroup: '.radioGroup'
            }, options);
            var noImagesLabel = container.find('.no-images-label');

            function refreshRadioGroup() {
                var checkboxes = container.find(settings.isPrimaryGroup);
                checkboxes.unbind('click.radioGroup');
                checkboxes.bind('click.radioGroup', function() {
                    $('input[rel="' + $(this).attr('rel') + '"]').removeAttr('checked');
                    $(this).attr('checked', 'checked');
                });
            }

            function removeImageItem() {
                if (!confirm('Are you sure you want to delete this image?')) {
                    return false;
                }
                $(this).closest('.image-attachment-item').remove();
                if (!container.find('.image-attachment-item').length) {
                    noImagesLabel.show();
                }

                return false;
            }

            container.find(settings.removeBtn).click(removeImageItem);
            container.find(settings.addBtn).click(function () {
                noImagesLabel.hide();
                var prototype   = $('#' + container.prop('id') + '_prototype').html();
                var countImages = container.find('.image-attachment-item').length;
                prototype       = $(prototype.replace(/__name__/g, countImages));

                prototype.prependTo(container.find('.new-uploads-container'));
                prototype.find(settings.removeBtn).click(removeImageItem);
                refreshRadioGroup();

                return false;
            });

            refreshRadioGroup();
        };
    })(jQuery);
}