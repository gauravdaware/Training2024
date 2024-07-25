/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @api */
define([
    'Magento_Checkout/js/view/payment/default'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'I95Dev_PoNumber/payment/checkmo',
            purchaseOrderNumber: ''
        },
        /** @inheritdoc */
        initObservable: function () {
            console.log('checkmo-method');
            this._super()
                .observe(['purchaseOrderNumber']); // Observe the new field

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
                    'po_number': this.purchaseOrderNumber()
                }
            };
        },
        /**
         * @return {jQuery}
         */
        // validate: function () {
        //     var form = 'form[data-role=checkmo-form]';
        //
        //     return $(form).validation() && $(form).validation('isValid');
        // },
        /**
         * Returns send check to info.
         *
         * @return {*}
         */
        getMailingAddress: function () {
            return window.checkoutConfig.payment.checkmo.mailingAddress;
        },

        /**
         * Returns payable to info.
         *
         * @return {*}
         */
        getPayableTo: function () {
            return window.checkoutConfig.payment.checkmo.payableTo;
        }
    });
});
