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
            template: 'I95Dev_PoNumber/payment/cashondelivery',
            purchaseOrderNumber: ''
        },
        /** @inheritdoc */
        initObservable: function () {
            console.log('cashondelivery-method');
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
         * Returns payment method instructions.
         *
         * @return {*}
         */
        getInstructions: function () {
            return window.checkoutConfig.payment.instructions[this.item.method];
        }
    });
});
