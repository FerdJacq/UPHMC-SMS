/*
 * Copyright IBM Corp. All Rights Reserved.
 *
 * SPDX-License-Identifier: Apache-2.0
 */

'use strict';

const transaction = require('./lib/Transaction');

module.exports.Transaction = transaction;
module.exports.contracts = [transaction];
