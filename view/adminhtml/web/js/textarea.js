define([
    'jquery',
    'Magento_Ui/js/form/element/textarea',
    'Magento_Ui/js/modal/modal',
    'text!DarshilTech_SmsNotification/json/variable.json',
    'mage/translate',
    'ko'
], function ($, Textarea, modal, Variable, $t, ko) {
    'use strict';

    return Textarea.extend({
        defaults: {
            cols: 15,
            rows: 15,
            elementTmpl: 'DarshilTech_SmsNotification/form/textarea',
        },

        initialize: function () {
            this._super();

            let parsed = JSON.parse(Variable);
            this.variables = ko.observableArray(
                Object.keys(parsed).map(key => ({
                    key,
                    childVariables: parsed[key],
                    // Bind the function here to maintain context
                    insertVariable: this.insertVariable.bind(this)
                }))
            );

            // Delay to ensure Knockout templates render first
            setTimeout(()=>{
                this._initModal();
            },500)


            return this;
        },

        _initModal: function () {
            const self = this;
            const modalEl = $('#variables-chooser');

            if (!modalEl.length) {
                console.warn('Modal element not found');
                return;
            }
            modalEl.show();
            this.modal = modal({
                title: $t('Insert Variable'),
                type: 'slide',
                buttons: [],
                closed: function () {
                   
                }
            }, modalEl);

            // Ensure modal is hidden initially
            modalEl.hide();
        },

        openDialogWindow: function () {
            if (this.modal) {
                $('#variables-chooser').show();
                this.modal.openModal();
            }
        },

        closeDialogWindow: function () {
            if (this.modal) {
                this.modal.closeModal();
            }
        },

        insertVariable: function (item) {
            const textarea = document.getElementById(this.uid);
        
            if (textarea) {
                // Force focus before getting selection range
                textarea.focus();
        
                // Get cursor/selection position
                const start = textarea.selectionStart;
                const end = textarea.selectionEnd;
        
                const currentValue = this.value();
                const variable = item.variable; // Make sure `variable` exists in `item`
        
                // Insert variable at cursor position
                const updatedValue = currentValue.substring(0, start) + variable + currentValue.substring(end);
                this.value(updatedValue);
        
                // Optional: set cursor after inserted variable
                setTimeout(() => {
                    textarea.selectionStart = textarea.selectionEnd = start + variable.length;
                }, 0);
            }
        
            this.closeDialogWindow();
        }
        
    });
});
