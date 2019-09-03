/* global Drupal, jQuery */

var $ = jQuery

class AutocompleteElement {
  constructor ($element, settings) {
    this.$element = $element
    this.settings = $.extend(settings, { select2: {} })
  }
  select2Config () {
    return $.extend(true, {
      minimumInputLength: 2,
      ajax: {
        url: Drupal.settings.autocomplete_api.endpoint,
        data: this.buildData,
        processResults: this.processResults.bind(this),
        dataType: 'json',
        delay: 250,
        beforeSend: function (xhr) {
          xhr.setRequestHeader('Authorization', Drupal.settings.autocomplete_api.apiKey)
        }
      }
    }, this.settings.select2, {
      dropdownParent: this.$element.parent()
    })
  }
  bindSelect2 () {
    this.$element.select2(this.select2Config())
  }
  buildData (params) {
    return {
      search: params.term,
      count: 20,
      offset: ((params.page || 1) - 1) * 20
    }
  }
  processResults (data, page) {
    return {
      results: data.values.map(function (o) {
        return { id: JSON.stringify(o), text: o.label }
      }),
      more: page * 20 < data.total_items
    }
  }
}

export { AutocompleteElement }
