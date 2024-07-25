/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @api */
define([
    'ko',
    'Magento_Checkout/js/view/payment/default'
], function (ko, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'I95Dev_PoNumber/payment/banktransfer',
            purchaseOrderNumber: ''
        },
        /** @inheritdoc */
        initObservable: function () {
            console.log('banktransfer-method');
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
         * Get value of instruction field.
         * @returns {String}
         */
        getInstructions: function () {
            return window.checkoutConfig.payment.instructions[this.item.method];
        }
    });
});
