/**
 * Created by vagenas on 27/12/2014.
 */
/*!
 *
 *
 * Copyright: 2014 Panagiotis Vagenas
 *
 * @author Panagiotis Vagenas <pan.vagenas@gmail.com>
 * @since 150120
 */

(function ($)
{
  var extension = $xd_v141226_dev.$.extension_class('totos', '$ajax');

  $totos.$ajax = new extension();

  /**
   * Performs the request to generate the totos.xml file
   * @param $modalContainer
   */
  $totos.$ajax.generateXMLNow = function ($modalContainer)
  {
    var $genNowBtn = jQuery('.generate-now');

    $totos.$ajax.get(
     '©ajax.®ajaxGenerateTotosXML',
     $totos.$ajax.___private_type,
     [],
     {
       complete  : function (response)
       {
         var json = response.responseJSON;
         if (json.success != true || json.data.productsUpdated == undefined) {
           $modalContainer.find('.modal-title').html('<p class="bg-danger">' + json.data + '</p>');
         } else {
           $modalContainer.find('.modal-title').html('<p class="bg-success">Generation Complete! ' + json.data.productsUpdated + ' products included in XML</p>');
         }
         $genNowBtn.removeClass('disabled').removeClass('loading');
       },
       beforeSend: function ()
       {
         $genNowBtn.addClass('disabled').addClass('loading');
       }
     }
    );
  };

  /**
   *
   * @param text
   */
  $totos.$ajax.copyToClipboard = function (text)
  {
    window.prompt("Copy to clipboard: Ctrl+C, Enter", text);
  };

  $('.generate-now').click(function ()
  {
    $totos.$ajax.generateXMLNow($('#generateNowModal'));
  });

  $('#show-advanced').change(function (e)
  {
    e.preventDefault();
    if ($(this).is(':checked')) {
      $('.main-settings-form-wrapper').find('.advanced').slideDown('fast');
    } else {
      $('.main-settings-form-wrapper').find('.advanced').slideUp('fast');
    }
  })
})(jQuery);
