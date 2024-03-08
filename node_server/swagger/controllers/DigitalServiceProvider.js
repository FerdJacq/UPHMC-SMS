'use strict';

var utils = require('../utils/writer.js');
var DigitalServiceProvider = require('../service/DigitalServiceProviderService');

module.exports.addTransaction = function addTransaction (req, res, next, body, dspCode, dspToken, dspSecret) {
  DigitalServiceProvider.addTransaction(body, dspCode, dspToken, dspSecret)
    .then(function (response) {
      utils.writeJson(res, response);
    })
    .catch(function (response) {
      utils.writeJson(res, response);
    });
};

module.exports.updateTransaction = function updateTransaction (req, res, next, body, dspCode, dspToken, dspSecret) {
  DigitalServiceProvider.updateTransaction(body, dspCode, dspToken, dspSecret)
    .then(function (response) {
      utils.writeJson(res, response);
    })
    .catch(function (response) {
      utils.writeJson(res, response);
    });
};
