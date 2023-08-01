/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import RequestSender from "./RequestSender.js";

let errorBlock = document.getElementById('error_block');
let paymentInfoBlock = document.getElementById('payment_info_block');



document.addEventListener('DOMContentLoaded', function () {
  let productElem = document.getElementById("calculate_form_product");
  let taxNumberElem = document.getElementById("calculate_form_tax_number");
  let couponCodeElem = document.getElementById("calculate_form_coupon_code");
  let paymentProcessorElem = document.getElementById("calculate_form_payment_processor");

  let calculateForm = document.getElementById('calculate_form');

  calculateForm.onsubmit = async function(e){
      e.preventDefault();
      let formAction = this.action;
      
      hideAndResetAllInfoBlocks();
      hidePayBtn();

      let postBody = JSON.stringify({
        product: productElem.value,
        taxNumber: taxNumberElem.value,
        couponCode: couponCodeElem.value,
        paymentProcessor: paymentProcessorElem.value
      });

      let response = await RequestSender.sendPostRequest(formAction, postBody);
      let calculateResult = await response.json();

      if(calculateResult.success === false){
        showErrorBlock(calculateResult.error[0]);
        return;
      }

      let paymentInfoMessage = 'Total price: ' + calculateResult.data.totalPrice.toString() + '. Please click the "Pay" button to finish the process.';

      showPaymentInfoBlock(paymentInfoMessage);
      showPayBtn();

      document.getElementById('pay_btn').onclick = async function(){

        hidePayBtn();
        hideAndResetAllInfoBlocks();

        let payPostBody = JSON.stringify({
          hash: calculateResult.data.hash,
          userPrice: calculateResult.data.totalPrice
        });

         let paymentResponse = await RequestSender.sendPostRequest('/api/pay', payPostBody);
         let paymentResult = await paymentResponse.json();

         if(paymentResult.success === false){
           showErrorBlock(paymentResult.error[0]);
           return;
         }

         showPaymentInfoBlock(paymentResult.data.message);

      };
  };

  
}, false);

function hideAndResetAllInfoBlocks()
{
    document.getElementById('error_block').innerHTML = '';
    document.getElementById('payment_info_block').innerHTML = '';
    document.getElementById('error_block').style.display = 'none';
    document.getElementById('payment_info_block').style.display = 'none';
}

function hidePaymentInfoBlock()
{

}

function showPaymentInfoBlock(message)
{
  document.getElementById('payment_info_block').innerHTML = message;
  document.getElementById('payment_info_block').style.display = 'block';
}

function hideErrorBlock()
{

}

function showErrorBlock(message)
{
  document.getElementById('error_block').innerHTML = message;
  document.getElementById('error_block').style.display = 'block';
}

function showPayBtn()
{
  document.getElementById('pay_btn').style.display = 'block'
}

function hidePayBtn()
{
  document.getElementById('pay_btn').style.display = 'none';

}