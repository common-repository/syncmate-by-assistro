document.addEventListener('DOMContentLoaded', function () {
    const tabLinks = document.querySelectorAll('#custom-tabs ul li a');
    const tabContents = document.querySelectorAll('#custom-tabs > div');

    tabLinks.forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            // Hide all tab contents
            tabContents.forEach(function (content) {
                content.style.display = 'none';
            });

            // Show the selected tab content
            const targetId = link.getAttribute('href').substring(1);
            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.style.display = 'block';
            }
        });
    });

  
});


// jQuery(document).ready(function($) {
//     // Attach the click event to your button with an ID of 'testConnectionButton'
//     $('#testConnectionButton').on('click', function(event) {
//         event.preventDefault();
//         const user_company_id = $('#wapushplus_webhook_get').val();
//         const apiUrl = "https://app.assistro.co/api/wapushplus/checkconnection";
//         // alert(apiUrl);
        
//         fetch(apiUrl)
//             .then((response) => response.json())
//             .then((response) => {
//                 var data = response.data;
//                 // alert(data); 

//                 toastr.options = {
//                     // Notification options
//                 };

//                 if (data.statusCode == 1 && data.status == 200) {
//                     toastr.success("You are connected to WhatsApp Web. To check, you would have received a message on WhatsApp from yourself");
//                 } else {
//                     toastr.error("You are not connected to WhatsApp Web. Please try to reconnect with your WhatsApp Web.");
//                 }
//             })
//             .catch((error) => {
//                 alert(error);
//                 console.error('Error not connecting client:', error);
//             });
//         alert("error");
//     });
// });

jQuery(document).ready(function($) {
    $('#testConnectionButton').on('click', function(event) {
        event.preventDefault();
        const jwtToken = document.getElementById('assistro-key-push-plus').value;
        // console.log(jwtToken);
        // const jwtToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwMDAvcHJvZHVjdHMvd2FwdXNoLXBsdXMvc2V0dGluZ3MvYXBpLWdlbmVyYXRlLzMwL2NvbmZpZ3VyYXRpb24iLCJpYXQiOjE2OTgyMTU4MTYsImV4cCI6MTcwMDgwNzgxNiwibmJmIjoxNjk4MjE1ODE2LCJqdGkiOiJDd3p2NDMzWkhjTDRCOUZ6Iiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjciLCJ1c2VyX2NvbXBhbnlfaWQiOjEsInVzZXJfcHVzaF9wbHVzX3dlYmhvb2tfaWQiOjMwLCJjdXJyZW50X3dlYmhvb2tfaWQiOjMwLCJwcm9kdWN0X2lkIjo1LCJpbnRlZ3JhdGlvbiI6ImNvbmZpZ3VyYXRpb24ifQ.8IQOp59IGKrD16_KM2m4kqVZ-5BejdK_hf4R19rXhOA";
        

        // Set up the AJAX request with headers
        $.ajax({
            type: 'POST',
            url: "https://app.assistro.co/api/wapushplus/checkconnection",
            headers: {
                'Authorization': 'Bearer ' + jwtToken, // Include the JWT token in the 'Authorization' header
            },
            success: function(response) {
                // Handle the response from the server
                console.log(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
                // Handle AJAX errors
                console.error('AJAX error:', textStatus, errorThrown);
            }
        });
    });
});






