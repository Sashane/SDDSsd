const items = {
    'Black Coffee': 500,
    'Mocha': 700,
    'Latte': 800,
    'Red Velvet Cake': 800,
    'Club Sandwich': 600,
    'Butter Croissant': 400
};

let cart = {};
let currentPage = 1;

function updateQuantity(item, change) {
    cart[item] = (cart[item] || 0) + change;
    if (cart[item] < 0) cart[item] = 0;
    document.getElementById(`qty-${item}`).textContent = cart[item];
    updateSummary();
}

function updateSummary() {
    const summaryItems = document.getElementById('summary-items');
    const totalPrice = document.getElementById('total-price');
    const cartCounter = document.querySelector('.cart-counter'); // Select the cart counter
    let total = 0;
    let totalItems = 0; // Track total number of items
    summaryItems.innerHTML = '';
    
    for (const item in cart) {
        if (cart[item] > 0) {
            const itemTotal = cart[item] * items[item];
            total += itemTotal;
            totalItems += cart[item]; // Add quantity to total items
            const p = document.createElement('p');
            p.textContent = `${item} x ${cart[item]}: Rs. ${itemTotal}`;
            summaryItems.appendChild(p);
        }
    }
    
    totalPrice.textContent = total;
    cartCounter.textContent = totalItems; // Update cart counter
    document.getElementById('next-to-address').disabled = total === 0;
}

function goToPage(page) {
    if (page === 2 && Object.values(cart).every(qty => qty === 0)) return;
    if (page === 3) {
        const form = document.getElementById('address-form');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
    }
    
    document.getElementById(`page${currentPage}`).style.display = 'none';
    document.getElementById(`page${page}`).style.display = 'block';
    currentPage = page;
    
    const progressLine = document.getElementById('progressLine');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    
    if (page >= 2) {
        progressLine.style.width = '50%';
        progressLine.classList.add('active');
        step2.classList.remove('step-inactive');
    } else {
        progressLine.style.width = '0%';
        progressLine.classList.remove('active');
        step2.classList.add('step-inactive');
        step3.classList.add('step-inactive');
    }
    
    if (page === 3) {
        progressLine.style.width = '100%';
        step3.classList.remove('step-inactive');
        updateFinalSummary();
    }
}

function updateFinalSummary() {
    const finalSummary = document.getElementById('final-summary');
    const subtotal = document.getElementById('subtotal');
    const discount = document.getElementById('discount');
    const delivery = document.getElementById('delivery');
    const finalTotal = document.getElementById('final-total');
    
    let total = 0;
    finalSummary.innerHTML = '';
    
    for (const item in cart) {
        if (cart[item] > 0) {
            const itemTotal = cart[item] * items[item];
            total += itemTotal;
            const p = document.createElement('p');
            p.textContent = `${item} x ${cart[item]}: Rs. ${itemTotal}`;
            finalSummary.appendChild(p);
        }
    }
    
    subtotal.textContent = total;
    const discountAmount = total > 2000 ? total * 0.1 : 0;
    discount.textContent = discountAmount;
    const deliveryCharge = 100;
    delivery.textContent = deliveryCharge;
    finalTotal.textContent = total - discountAmount + deliveryCharge;
}

function confirmOrder() {
    alert('Order confirmed! Thank you for your purchase.');
    cart = {};
    goToPage(1);
    for (const item in items) {
        document.getElementById(`qty-${item}`).textContent = '0';
    }
    updateSummary();
    document.querySelector('.cart-counter').textContent = '0'; // Reset cart counter
}