/**
 * number_format(number, decimals, decPoint, thousandsSep) in JavaScript, known from PHP.
 * It formats a number to a string with grouped thousands, with custom seperator and custom decimal point
 * @param {number} number - number to format
 * @param {number} [decimals=0] - (optional) count of decimals to show
 * @param {string} [decPoint=.] - (optional) decimal point
 * @param {string} [thousandsSep=,] - (optional) thousands seperator
 * @author Felix Leupold <felix@xiel.de>
 */
function number_format(number, decimals, decPoint, thousandsSep) {
    decimals = Math.abs(decimals) || 0;
    number = parseFloat(number);

    if (!decPoint || !thousandsSep) {
        decPoint = '.';
        thousandsSep = ',';
    }

    var roundedNumber = Math.round(Math.abs(number) * ('1e' + decimals)) + '';
    var numbersString = decimals ? (roundedNumber.slice(0, decimals * -1) || 0) : roundedNumber;
    var decimalsString = decimals ? roundedNumber.slice(decimals * -1) : '';
    var formattedNumber = "";

    while (numbersString.length > 3) {
        formattedNumber += thousandsSep + numbersString.slice(-3)
        numbersString = numbersString.slice(0, -3);
    }

    if (decimals && decimalsString.length === 1) {
        while (decimalsString.length < decimals) {
            decimalsString = decimalsString + decimalsString;
        }
    }

    return (number < 0 ? '-' : '') + numbersString + formattedNumber + (decimalsString ? (decPoint + decimalsString) : '');
}


// just for demo purposes...
// var log = console.log;

// log('-- english format --');
// log(number_format(1234.50, 2)); // ~> "1,234.50"
// log(number_format(1234, 2)); // ~> "1,234.00"
// log(number_format(0, 2)); // ~> "0.00"
// log(number_format(-0.5, 2)); // ~> "-0.50"
// log(number_format(299.99)); // ~> "300"
// log(number_format(-299.9, 4)); // ~> "-299.9000"

// // german format
// log('-- german format --');
// log(number_format(1234.50, 2, ',', '.')); // ~> "1.234,50"
// log(number_format(0.5, 2, ',', '.')); // ~> "0,50"

// // french format
// log('-- french format --');
// log(number_format(1234.50, 2, '.', ' ')); // ~> "1 234.50"
// log(number_format(1233.999, 2, '.', ' ')); // ~> "1 234.00"
