const adyenEncrypt = require('node-adyen-encrypt')(process.env.ADYEN_ENCRYPTION_VERSION);
const options = {};
const genTime = new Date().toISOString();
const ccData = {
    generationtime: genTime,
    holderName: process.argv[2] + ' ' + process.argv[3],
    number: process.argv[4]
};
const mmData = {
    generationtime: genTime,
    holderName: process.argv[5] + ' ' + process.argv[2],
    expiryMonth: process.argv[6]
};
const yyyyData = {
    holderName: process.argv[3] + ' ' + process.argv[2],
    generationtime: genTime,
    expiryYear: process.argv[7]
};
const cvvData = {
    holderName: process.argv[3] + ' ' + process.argv[2],
    generationtime: genTime,
    cvc: process.argv[8]
};
const encCCData = adyenEncrypt.createEncryption(process.env.ADYEN_ENCRYPTION_KEY, options).encrypt(ccData);
const encMMData = adyenEncrypt.createEncryption(process.env.ADYEN_ENCRYPTION_KEY, options).encrypt(mmData);
const encYYYYData = adyenEncrypt.createEncryption(process.env.ADYEN_ENCRYPTION_KEY, options).encrypt(yyyyData);
const encCVVData = adyenEncrypt.createEncryption(process.env.ADYEN_ENCRYPTION_KEY, options).encrypt(cvvData);
console.log(encCCData);
console.log(encMMData);
console.log(encYYYYData);
console.log(encCVVData);
