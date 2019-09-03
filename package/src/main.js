/* global Drupal, jQuery */

import { AutocompleteElement } from './autocomplete-element'

var $ = jQuery

Drupal.behaviors.autocomplete_api = {
  attach: function (context, settings) {
    let configs = settings.autocomplete_api.elements
    for (let htmlId in configs) {
      (new AutocompleteElement($(`#${htmlId}`, context), configs[htmlId])).bindSelect2()
    }
  }
}

Drupal.webform = Drupal.webform || {}
Drupal.webform.conditionalOperatorAutocompleteEqual = function (element, existingValue, ruleValue) {
  var data, e
  if ($(element).closest('.webform-conditional-hidden').length > 0) {
    return false
  }
  try {
    data = JSON.parse($('input', element).select2('data').id)
    return data.key === ruleValue || data.label === ruleValue
  }
  catch (error) {
    e = error
    return false
  }
}
Drupal.webform.conditionalOperatorAutocompleteNotEqual = function (element, existingValue, ruleValue) {
  return !Drupal.webform.conditionalOperatorAutocompleteEqual(element, existingValue, ruleValue)
}
