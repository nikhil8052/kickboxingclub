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
    <form id="payment-form" action="{{ url('/wordpress/storepayment') }}" method="GET">
        @csrf
        <div id="card-element"></div>
        <input type="hidden" name="payment_method" id="payment_method" >
        <button id="submit-button" onclick="submitForm(this) ">Submit Payment</button>
    </form>



    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

    <!-- <script>
        // Your Stripe publishable API key
        const stripe = Stripe('pk_live_lYDT2GeFPx1rfYP5OcPsT5fA00CBSRGphg'); 

        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');


        async function  submitForm(){

            event.preventDefault();
            submitButton.disabled = true;


            const { paymentMethod, error } = await stripe.createPaymentMethod('card', cardElement);

            if (error) {
                // Display error.message in your UI
                console.error(error);
                submitButton.disabled = false;
            } else {
                // Send paymentMethod.id to your server

              $('#payment_method').val(paymentMethod.id);
              $('#payment-form').submit();


            }


        }

        
        // form.addEventListener('submit', async (event) => {
        //     event.preventDefault();
        //     submitButton.disabled = true;

        //     const { paymentMethod, error } = await stripe.createPaymentMethod('card', cardElement);

        //     if (error) {
        //         // Display error.message in your UI
        //         console.error(error);
        //         submitButton.disabled = false;
        //     } else {
        //         // Send paymentMethod.id to your server

        //       $('#payment_method').val(paymentMethod.id);

        //     }
        // });
    </script> -->



       <script>
        // Initialize Stripe with the publishable key
        var stripe_publishable_api_key=`{{ $stripe_publishable_api_key }}`;
        var client_secret= `{{ $client_secret }}`;
        var stripe_setup_intent_id= `{{ $stripe_setup_intent_id }}`;


        const stripe = Stripe(stripe_publishable_api_key); // Replace with the correct key from your server
        const elements = stripe.elements();
        const cardElement = elements.create('card');
        cardElement.mount('#card-element');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit-button');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        async function submitForm(event) {
            event.preventDefault();
            submitButton.disabled = true;

            const { paymentMethod, error } = await stripe.createPaymentMethod('card', cardElement);

            if (error) {
                console.error(error);
                submitButton.disabled = false;
            } else {
                // Retrieve Setup Intent data from your server (you should have a way to get this data)
                const setupIntentData = {
                    stripe_setup_intent_id:stripe_setup_intent_id,
                    client_secret: client_secret,
                    stripe_publishable_api_key:stripe_publishable_api_key
                };

                // Confirm the card setup with the client secret from your server
                const { error: confirmError, setupIntent } = await stripe.confirmCardSetup(
                    setupIntentData.client_secret,
                    {
                        payment_method: paymentMethod.id
                    }
                );

                if (confirmError) {
                    console.error(confirmError);
                    submitButton.disabled = false;
                } else {
                    // Handle successful confirmation
                    console.log('Setup Intent confirmed successfully:', setupIntent);
                    $('#payment_method').val(paymentMethod.id);
                    $('#payment-form').submit();
                }
            }
        }

        form.addEventListener('submit', submitForm);
    </script>




</body>
</html>
