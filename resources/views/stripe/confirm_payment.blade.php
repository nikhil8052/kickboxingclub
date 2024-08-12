
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment</title>
    <script src="https://js.stripe.com/v3/"></script>
       <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

<form id="payment-form">
    <div id="card-element">
    <!-- A Stripe Element will be inserted here. -->
    </div>

    <!-- Used to display form errors. -->
    <div id="card-errors" role="alert"></div>

    <button id="submit">Pay</button>
</form>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>


// {
//     "stripe_payment_intent_id": "pi_3PiVbJI1kLQM3y1J0j980A7A",
//     "stripe_publishable_api_key": "pk_live_W3fIw3XQnZzGG45Yi7R4pK44",
//     "client_secret": "pi_3PiVbJI1kLQM3y1J0j980A7A_secret_WjD9NUi9PkFcfZg7XTNSfea3c"
// }


// Replace with your Stripe publishable key
const stripe = Stripe('pk_live_W3fIw3XQnZzGG45Yi7R4pK44');
const elements = stripe.elements();
const cardElement = elements.create('card');
cardElement.mount('#card-element');

const form = document.getElementById('payment-form');
form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const {error, paymentIntent} = await stripe.confirmCardPayment(
        'pi_3PiVtYI1kLQM3y1J0GcoPoUn_secret_l84eJou5fQEm52IpNcMNDumjQ', 
        {
            payment_method: {
                card: cardElement,
                billing_details: {
                    name: 'Cardholder Name',
                },
            }
        }
    );

    if (error) {
        // Display error to the user
        document.getElementById('card-errors').textContent = error.message;
    } else {
        // The payment has been processed!
        if (paymentIntent.status === 'succeeded') {
            // Show a success message to your customer
            alert('Payment successful!');
        }
    }
});


    </script>
</body>
</html>





