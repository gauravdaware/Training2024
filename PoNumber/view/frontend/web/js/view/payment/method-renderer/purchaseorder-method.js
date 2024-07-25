define([
    'Magento_Checkout/js/view/payment/default',
    'jquery',
    'mage/validation'
], function (Component, $) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'I95Dev_PoNumber/payment/purchaseorder-form',
            purchaseOrderNumber: '',
            purchaseOrderComment: '' // Add this line
        },

        /** @inheritdoc */
        initObservable: function () {
            console.log('purchaseorder-method');
            this._super()
                .observe(['purchaseOrderNumber', 'purchaseOrderComment']); // Observe the new field

            return this;
        },

        /**
         * @return {Object}
         */
        getData: function () {
            return {
                method: this.item.method,
                'po_number': this.purchaseOrderNumber(),
                'additional_data': {
                    'po_number': this.purchaseOrderNumber(),
                    'paymentpocomment': this.purchaseOrderComment() // Use the observed field
                }
            };
        },

        /**
         * @return {jQuery}
         */
        validate: function () {
            var form = 'form[data-role=purchaseorder-form]';

            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
