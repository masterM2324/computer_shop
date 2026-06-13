function addToCart(productId, quantity) {
    // បម្លែងតម្លៃ quantity ទៅជាលេខ
    quantity = parseInt(quantity);
    console.log("Product ID:", productId, "Quantity:", quantity);

    if (quantity <= 0 || isNaN(quantity)) {
        Swal.fire({
            icon: 'error',
            title: 'ខុសឆ្គង!',
            text: 'សូមបញ្ចូលចំនួនទំនិញយ៉ាងតិច ១'
        });
        return;
    }

    // ឆែកមើលលក្ខខណ្ឌមិនទាន់ Login (isLoggedIn ត្រូវប្រកាសក្នុងឯកសារ PHP)
    if (typeof isLoggedIn !== 'undefined' && !isLoggedIn) {
        Swal.fire({
            icon: 'warning',
            title: 'សូមចូលគណនីជាមុនសិន!',
            text: 'អ្នកត្រូវតែ Login ចូលប្រព័ន្ធទើបអាចថែមទំនិញចូលកន្ត្រកបាន។',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'ទៅកាន់ទំព័រ Login',
            cancelButtonText: 'បោះបង់'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'login.php'; // នាំផ្លូវទៅកាន់ទំព័រ Login
            }
        });
        return; // បញ្ឈប់កូដមិនឱ្យទៅ Fetch ឡើយ
    }

    // ប្រសិនបើបាន Login ហើយ វានឹងដំណើរការ Fetch ទៅកាន់ add_to_cart.php ធម្មតា
    let formData = new FormData();
    formData.append('product_id', productId);
    formData.append('quantity', quantity); 

    fetch('add_to_cart.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            if (document.getElementById('cart-count')) {
                document.getElementById('cart-count').innerText = data.cart_count;
            }
            Swal.fire({
                icon: 'success',
                title: 'ជោគជ័យ!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'មានបញ្ហា!',
                text: data.message
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'មានបញ្ហា!',
            text: 'មិនអាចទាក់ទងទៅកាន់ Server បានទេ'
        });
    });
}

function toggleFavorite(productId, element) {
    const icon = element.querySelector('i');
    let formData = new FormData();
    formData.append('product_id', productId);

    fetch('toggle_favorite.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'added') {
                icon.classList.replace('far', 'fas');
                Swal.fire({
                    icon: 'success',
                    title: 'ជោគជ័យ!',
                    text: 'បានបន្ថែមទៅក្នុងបញ្ជីដែលចូលចិត្ត',
                    showConfirmButton: false,
                    timer: 1500,
                });
            } else if (data.status === 'removed') {
                icon.classList.replace('fas', 'far');
                Swal.fire({
                    icon: 'info',
                    title: 'បានលុបចេញ!',
                    text: 'ផលិតផលត្រូវបានដកចេញពីបញ្ជីចូលចិត្ត',
                    showConfirmButton: false,
                    timer: 1500,
                });
            } else if (data.status === 'not_logged_in') {
                Swal.fire({
                    icon: 'warning',
                    title: 'សូមចូលប្រើប្រាស់!',
                    text: 'អ្នកត្រូវតែ Login ជាមុនសិនដើម្បីប្រើមុខងារនេះ',
                    confirmButtonText: 'ទៅកាន់ទំព័រ Login',
                    confirmButtonColor: '#3085d6',
                    showCancelButton: true,
                    cancelButtonText: 'បោះបង់'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'login.php';
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'មានបញ្ហា!',
                text: 'មិនអាចទាក់ទងទៅកាន់ Server បានទេ'
            });
        });
}
