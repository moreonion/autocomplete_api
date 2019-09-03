/* global Drupal, jQuery */

var $ = jQuery
Drupal.behaviors.autocomplete_api = {
  buildData: function(params) {
    return {
      search: params.term,
      count: 20,
      offset: ((params.page || 1) - 1) * 20
    };
  },
  processResults: function(data, page) {
    return {
      results: data.values.map(function(o) {
        return {
          id: JSON.stringify(o),
          text: o.label
        };
      }),
      more: page * 20 < data.total_items
    };
  },
  defaultConfig: function() {
    return {
      minimumInputLength: 2,
      ajax: {
        url: Drupal.settings.autocomplete_api.endpoint,
        data: this.buildData,
        processResults: this.processResults,
        dataType: 'json',
        delay: 250,
        beforeSend: function(xhr) {
          xhr.setRequestHeader('Authorization', Drupal.settings.autocomplete_api.apiKey);
        }
      }
    };
  },
  attach: function(context, settings) {
    var $element, config, element_config, html_id, parent_config, ref;
    ref = settings.autocomplete_api.elements;
    for (html_id in ref) {
      element_config = ref[html_id];
      $element = $(`#${html_id}`, context);
      parent_config = {
        dropdownParent: $element.parent()
      };
      config = $.extend(true, {}, this.defaultConfig(), element_config, parent_config);
      $element.select2(config);
    }
  }
};
Drupal.webform = Drupal.webform || {};
Drupal.webform.conditionalOperatorAutocompleteEqual = function(element, existingValue, ruleValue) {
  var data, e;
  if ($(element).closest('.webform-conditional-hidden').length > 0) {
    return false;
  }
  try {
    data = JSON.parse($('input', element).select2('data').id);
    return data.key === ruleValue || data.label === ruleValue;
  } catch (error) {
    e = error;
    return false;
  }
};
Drupal.webform.conditionalOperatorAutocompleteNotEqual = function(element, existingValue, ruleValue) {
  return !Drupal.webform.conditionalOperatorAutocompleteEqual(element, existingValue, ruleValue);
};
