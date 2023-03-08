/* global Drupal, jQuery */

var $ = jQuery

class AutocompleteElement {
  constructor ($element, settings) {
    this.$element = $element
    this.settings = $.extend({}, { select2: {} }, settings)
  }
  select2Config () {
    return $.extend(true, {
      minimumInputLength: 2,
      ajax: {
        url: Drupal.settings.autocomplete_api.endpoint,
        data: (params) => {
          return this.buildData(params)
        },
        processResults: (data, page) => {
          return this.processResults(data, page)
        },
        dataType: 'json',
        delay: 250,
        beforeSend: function (xhr) {
          xhr.setRequestHeader('Authorization', 'Bearer ' + Drupal.settings.autocomplete_api.token)
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
      count: this.settings.count,
      offset: ((params.page || 1) - 1) * this.settings.count
    }
  }
  processResults (data, page) {
    return {
      results: data.values.map(function (o) {
        return { id: JSON.stringify(o), text: o.label }
      }),
      more: page * this.settings.count < data.total_items
    }
  }
}

export { AutocompleteElement }
