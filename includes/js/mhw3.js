var cart_products = new Array();
var total_Cart = 0;
var total_cart_items = 0;
var logged_user;

function onLoadHomePage(){
    setUI();
    showRandomProductsCategory_mydb();
    getLoggedUser();
    showCategory();
    buildCart();
    buildFavouritesSection();
    console.log("screen width: " + window.screen.width);
    if (window.screen.width < 600) {
        console.log("screen width: " + window.screen.width);
        document.querySelector("#shopping-cart-btn").addEventListener('click', openCartMobile);
    }
    setTimeout(slider, 5000);
}

function onLoadSearch(){
    const btn_favourite = document.querySelectorAll(".favourite-product-card");
    for (let btn of btn_favourite) {
        btn.addEventListener("click", handleFavourite);
    }
    
    const btn_collapse = document.querySelectorAll(".collapse");
    for (let btn of btn_collapse) {
        btn.addEventListener('click', collapseExpand);
    }
    
    const support_btn = document.querySelector("#support_btn");
    support_btn.addEventListener('click', toggleChat);
    
    const shopping_cart_content = document.querySelector("#shopping-cart");

    const btn_search = document.querySelector("#search_btn");
    btn_search.addEventListener('click', submitSearch);

    getLoggedUser();
    showCategory();
    buildCart();
    console.log("screen width: " + window.screen.width);
    if (window.screen.width < 600) {
        console.log("screen width: " + window.screen.width);
        document.querySelector("#shopping-cart-btn").addEventListener('click', openCartMobile);
    }
}

function getLoggedUser(){
    fetch("../api/user_handler.php?action=get_logged_user").then((response)=>{
        if(response.status === 200){
            response.json().then((response)=>{
                console.log("LOGGEDUSER: "+response);
                logged_user = response;
            }, onError);
        }
    }, onError);
}

function setUI(){
    const btn_favourite = document.querySelectorAll(".favourite-product-card");
    for (let btn of btn_favourite) {
        btn.addEventListener("click", handleFavourite);
    }
    
    const btn_collapse = document.querySelectorAll(".collapse");
    for (let btn of btn_collapse) {
        btn.addEventListener('click', collapseExpand);
    }
    
    const slider_img = document.querySelector("#slider_img");
    const btn_slider_back = document.querySelector("#back");
    const btn_slider_forward = document.querySelector("#forward");
    btn_slider_back.addEventListener('click', slider);
    btn_slider_forward.addEventListener('click', slider);
    
    const support_btn = document.querySelector("#support_btn");
    support_btn.addEventListener('click', toggleChat);

    const btn_search = document.querySelector("#search_btn");
    btn_search.addEventListener('click', submitSearch);
    
    const shopping_cart_content = document.querySelector("#shopping-cart");
}

/** */

function openCartMobile() {
    console.log("open mobile cart");
    shopping_cart_content.style.display = "block";
}

function closeCartMobile() {
    console.log("close mobile cart");
    shopping_cart_content.style.display = "none";
}

function removeCartProduct(event) {
    console.log("REMOVING...START");
    const product = event.currentTarget.parentNode;
    let index_p = null;
    console.log("before REMOIVING...: " + product.dataset.targetProductId);
    checkArrayCart();
    if ((index_p = productCartExist(product.dataset.targetProductId)) != null) {
        console.log("product removing exists index: " + index_p);
        updateTotalCart(cart_products[index_p].price, cart_products[index_p].qty, "del");
        for (let i = index_p; i < cart_products.length - 1; i++) {
            console.log("index: " + i);
            console.log("index + 1: " + (i + 1));
            console.log("length: " + cart_products.length);
            cart_products[i] = cart_products[i + 1];
        }
        cart_products.pop();
        event.currentTarget.parentNode.remove();
        console.log("after REMOIVING...: ");
        checkArrayCart();
        removeCartProduct_db(product.dataset.targetProductId);
        notify("Avviso", "Prodotto rimosso dal carello");
    } else {
        console.log("Product not exists: " + index_p);
    }
}

function removeCartProduct_db(id_item) {
    fetch("../api/cart_handler.php?action=del_item&id_item=" + id_item).then((response) => {
        if (response.status === 200) {
            response.json().then((response) => {
                console.log("REMOVED: " + JSON.stringify(response));
            }, onError);
        } else {
            console.log("ERROR")
        }
    }, onError);
}

/**
 * 
 * momentaneamente implementato con 2 immagini al solo scopo di utilizzare il comando "src" per cui il funzionamento alla pressione dei due tasti risulta analogo
 * e semplicistico più avanti verranno magari gestite immaggini attraverso un array 
 */
function slider(event) {
    //console.log("slider: "+slider_img.src);
    if (slider_img.src.search("1.png") !== -1) {
        slider_img.src = "../img/2.png";
    } else {
        slider_img.src = "../img/1.png";
    }
    setTimeout(slider, 5000);
}

/**
 * currentTarget target of listener associated
 * target of the object fire listener
 */
function handleFavourite(event) {
    const obj = event.currentTarget;
    if (obj.classList.contains("favourite-product-card-selected")) {
        obj.classList.remove("favourite-product-card-selected");
        handleFavourite_db("del", obj.parentNode.dataset.targetProductId);
        if(obj.parentNode.parentNode.id == "Favourites-container"){
            obj.parentNode.remove();
        }
        updateProductCardFavourites(obj.parentNode.dataset.targetProductId, "del");
        notify("Favourite", "Elemento rimosso dai preferiti");
    } else {
        obj.classList.add("favourite-product-card-selected");
        handleFavourite_db("add", obj.parentNode.dataset.targetProductId);
        notify("Favourite", "Elemento aggiunto ai preferiti");
        updateProductCardFavourites(obj.parentNode.dataset.targetProductId, "add");
    }
}

function updateProductCardFavourites(id_product, action){
    let card = document.querySelectorAll(".product-card");
    for(var c of card){
        card_id = c.dataset.targetProductId;
        console.log("product_id: "+id_product+", Card_id: "+card_id+", section: "+c.parentNode.id);
        if(card_id == id_product){
            console.log("TROVATO CARD")
            console.log("product_id: "+id_product+", Card_id: "+card_id+", section: "+c.parentNode.id);
            
            if(action == "del"){
                if(c.parentNode.id == "Favourites-container"){
                    c.remove();
                }else{
                    if (c.childNodes[0].classList.contains("favourite-product-card-selected")){
                        c.childNodes[0].classList.remove("favourite-product-card-selected");
                    } 
                }
                //nel caso che i preferiti compaiano altrove agirè qui infilando un else
            }else{
                fetch("../api/item_handler.php?action=get_item&id_item="+id_product).then((response)=>{
                    if(response.status === 200){    
                        response.json().then((response)=>{
                            response.preferito = 1;
                            addProduct(response, "Favourites");
                        }, onError);
                    }
                }, onError);
            }
            console.log("TROVATO CARD")
        }
    }
}

function handleFavourite_db(action, id_item) {
    console.log("favourite_db -> action: " + action + "-> item_id: " + id_item);
    if (action == "add") {
        fetch("../api/favourite_handler.php?action=add_favourite&id_item=" + id_item).then((response) => {
            if (response.status === 200) {
                response.json().then((response) => {
                    console.log("favourite_db: " + response);
                }, onError);
            }
        }, onError);
    } else if (action == "del") {
        fetch("../api/favourite_handler.php?action=del_favourite&id_item=" + id_item).then((response) => {
            if (response.status === 200) {
                response.json().then((response) => {
                    console.log("favourite_db: " + response);
                }, onError);
            }
        }, onError);
    }

}

//<span class="material-icons">chevron_right</span>
//.dataset accedo al dataset sfruttando il camel case posso recuperare tutti gli attributi data
function collapseExpand(event) {
    //console.log(event.currentTarget);
    const obj = document.querySelector(event.currentTarget.dataset.targetId);
    //console.log(obj);
    if (obj.classList.contains("collapsed")) {
        obj.classList.remove("collapsed");
    } else {
        obj.classList.add("collapsed");
    }
}

function notify(title, msg) {
    const notifyElement = document.createElement("div");
    const notifyTitle = document.createElement("h3");
    const notifyMsg = document.createElement("p");
    notifyTitle.textContent = title;
    notifyMsg.textContent = msg;
    notifyElement.appendChild(notifyTitle);
    notifyElement.appendChild(notifyMsg);
    notifyElement.classList.add("notify");
    notifyRemove();
    document.body.appendChild(notifyElement);
    setTimeout(notifyRemove, 5000);
}

function notifyRemove() {
    let notify = document.querySelectorAll(".notify");
    for (let obj of notify) {
        obj.remove();
    }
}

function toggleChat(event) {
    const support_container = document.querySelector("#support_container");
    toggle_value = event.currentTarget.dataset;
    if (toggle_value.value == 0) {
        /** BTN */
        //support_btn.style.width = "22%";
        support_btn.style.width = "300px";
        support_btn.style.borderRadius = "0px";
        /** CONTAINER CHAT */
        support_container.style.bottom = "88px";
        //support_container.style.width = "22%";
        //support_container.style.height = "55%";
        support_container.style.width = "300px";
        support_container.style.height = "500px";
        support_container.style.borderRadius = "0px";
        support_container.childNodes[1].style.display = "block";
        toggle_value.value = 1;
    } else if (toggle_value.value == 1) {
        /** CONTAINER CHAT */
        support_container.style.bottom = "20px";
        support_container.style.width = "70px";
        support_container.style.height = "70px";
        support_container.style.borderRadius = "50%";
        support_container.childNodes[1].style.display = "none";
        /** BTN */
        support_btn.style.width = "70px";
        support_btn.style.borderRadius = "50%";
        toggle_value.value = 0;
    }
}

function addSection(section_name, title_section) {
    let section_name_rep = section_name.replace(" ", "-");
    section_name_rep = section_name_rep.replace("'", "");
    const main_content = document.querySelector("#main-content");
    const section = document.createElement("section");
    const section_title = document.createElement("h2");
    const section_menu = document.createElement("span");
    const product_container = document.createElement("div");
    section.id = section_name_rep + "-section";
    section_title.textContent = title_section + section_name_rep;
    section_menu.classList.add("material-icons");
    section_menu.classList.add("collapse");
    section_menu.setAttribute("data-target-id", "#" + section_name_rep + "-container");
    section_menu.textContent = "expand_more";
    product_container.id = section_name_rep + "-container";
    product_container.classList.add("product-container");
    section.appendChild(section_title);
    section_title.appendChild(section_menu);
    section.appendChild(product_container);
    section_menu.addEventListener('click', collapseExpand);
    main_content.insertBefore(section, main_content.childNodes[4]);
}

function addProduct(product, section_name) {
    let section_name_rep = section_name.replace(" ", "-");
    section_name_rep = section_name_rep.replace("'", "");
    const section_container = document.querySelector("#" + section_name_rep + "-container");
    const product_card = document.createElement("div");
    const favourite = document.createElement("span");
    const product_img = document.createElement("img");
    const product_title = document.createElement("p");
    const product_price = document.createElement("h3");
    const product_price_full = document.createElement("small");
    const card_menu = document.createElement("span");
    product_card.setAttribute("data-target-product-id", product.id);
    product_card.classList.add("product-card");
    favourite.classList.add("material-icons");
    favourite.classList.add("favourite-product-card");
    if (product.preferito) {
        console.log("IS FAVOURITES: " + product.favourite)
        favourite.classList.add("favourite-product-card-selected");
    } else {
        console.log("IS NOT FAVOURITES: " + product.favourite)
    }
    favourite.textContent = "favorite_border";
    favourite.addEventListener("click", handleFavourite);
    product_img.src = product.image_path;
    product_title.classList.add("product-desc");
    product_title.textContent = product.title;
    product_price.classList.add("product-price");
    product_price.textContent = "EUR " + product.price
    product_price_full.textContent = "EUR " + Math.round(getRandomNumber((Number(product.price) + (Number(product.price) * 40) / 100), (product.price + (product.price * 73, 5) / 100)));
    card_menu.classList.add("material-icons");
    card_menu.classList.add("add-to-cart");
    card_menu.textContent = "add_shopping_cart";
    card_menu.addEventListener('click', gatSelectedProduct);
    product_card.appendChild(favourite);
    product_card.appendChild(product_img);
    product_card.appendChild(product_title);
    product_card.appendChild(product_price);
    product_price.appendChild(product_price_full);
    product_card.appendChild(card_menu);
    section_container.appendChild(product_card);
}

function gatSelectedProduct(e) {
    let product_id = e.currentTarget.parentNode.dataset.targetProductId;
    fetch('../api/item_handler.php?action=get_item&id_item=' + product_id).then((response) => {
        if (response.status === 200) {
            response.json().then((response) => {
                let product = response;
                product.qty = 1;
                addProductToCart_db(product);
                notify("Carrello", "Prodotto aggiunto nel carrello");
            }, onError);
        }
    }, onError);
}

/**
 * 
 * utiluizzato quando viene cliccato il tastto add to cart nella card dell'elemento
 */
function addProductToCart_db(product) {
    fetch('../api/cart_handler.php?action=add_item&id_item=' + product.id + '&qty=1').then((response) => {
        if (response.status === 200) {
            response.json().then((response) => {
                console.log(JSON.stringify(response));
                addProductToCart(product);
            }, onError);
        }
    }, onError);
}

/** passare un prodotto json obj 
 * utilizzato solo per aghgiungere i prodotti nel carrello lato client
 * es chiamato quando viene aperta la pagina
*/
function addProductToCart(product) {
    let index_p = productCartExist(product.id);
    if (index_p !== false) {
        console.log("index_p: " + index_p);
        cart_products[index_p].qty++;
        updateTotalCart(cart_products[index_p].price, 1, "ins");
        updateQtyCartProduct(cart_products[index_p]);
    } else {
        console.log("index_p: " + index_p);
        cart_products.push(product);
        const cart_product = document.createElement('div');
        const img_product = document.createElement('img');
        const col_product = document.createElement('div');
        const title_product = document.createElement('p');
        const price_product = document.createElement('h4');
        const qty_product = document.createElement('small');
        const delete_product = document.createElement('span');
        cart_product.classList.add('cart-product');
        cart_product.setAttribute('data-target-product-id', product.id);
        cart_product.setAttribute('data-target-price', product.price);
        cart_product.setAttribute('data-target-qty', product.qty);
        delete_product.classList.add('material-icons');
        delete_product.textContent = "delete";
        delete_product.addEventListener('click', removeCartProduct);
        img_product.src = product.image_path;
        title_product.textContent = product.title;
        price_product.textContent = "EUR " + product.price;
        updateTotalCart(product.price, product.qty, "ins");
        qty_product.textContent = "Q.ty: " + product.qty;
        col_product.classList.add('column');
        col_product.appendChild(title_product);
        col_product.appendChild(price_product);
        col_product.appendChild(qty_product);
        cart_product.appendChild(img_product);
        cart_product.appendChild(col_product);
        cart_product.appendChild(delete_product);
        document.querySelector('#shopping-cart').insertBefore(cart_product, document.querySelector('#cart-separator'));
    }
}

function productCartExist(product_id) {
    for (var i = 0; i < cart_products.length; i++) {
        if (cart_products[i].id == product_id) {
            console.log("Product cart exists");
            console.log("cart_products[i].id: " + cart_products[i].id)
            console.log("product_id: " + product_id);
            console.log("products exists: " + JSON.stringify(cart_products[i]));
            return i;
        }
    }
    return false;
}

function checkArrayCart() {
    for (let p of cart_products) {
        console.log("products array: " + JSON.stringify(p));
    }
}

function updateTotalCart(price, qty, op) {
    //console.log("Typeof: "+typeof price);
    //console.log("total before: "+total_Cart);
    if (op == "ins") {
        console.log("insert ups");
        total_Cart += Math.round((price * qty));
        total_cart_items += qty;
        //console.log("insert eur: "+price*qty);
    } else if (op == "del") {
        console.log("del ups");
        total_Cart -= Math.round((price * qty));
        total_cart_items -= qty;
        //console.log("removed eur: "+price*qty);
    }
    document.querySelector("#total_cart").textContent = "€" + total_Cart;
    document.querySelector("#total_cart_items").textContent = total_cart_items;
    //console.log("total after: "+total_Cart);
}

function getRandomNumber(min, max) {
    return Math.random() * (max - min) + min;
}

function onError(error) {
    console.log("fetch error: " + error);
}

function addMenuField(field, menu_id) {
    const categories = ["electronics", "jewelery", "men's clothing", "women's clothing"];
    const field_li = document.createElement("li");
    const field_a = document.createElement("a");
    field_li.appendChild(field_a);
    switch (field) {
        case "electronics":
            field_a.href = "elettronica.html";
            break;
        case "jewelery":
            field_a.href = "gioielli.html";
            break;
        case "men's clothing":
            field_a.href = "moda_uomo.html";
            break;
        case "women's clothing":
            field_a.href = "moda_donna.html";
            break;
        default:
            field_a.href = "https://fakestoreapi.com/products/category/" + field;
            break;
    }
    field_a.textContent = field;
    document.querySelector("#" + menu_id).appendChild(field_li);
}

function showCategory() {
    fetch("../api/categories_handler.php?action=list_categories").then((response) => {
        if (response.status === 200) {
            response.json().then((response) => {
                for (category of response) {
                    console.log("category: " + category);
                    addMenuField(category["nome"], "sub-nav");
                }
            }, onError);
        }
    }, onError);
}

function showAllProductsCategory(category_name) {
    console.log("category selected:" + category_name);
    fetch("https://fakestoreapi.com/products/category/" + category_name).then((response) => {
        if (response.status === 200) {
            response.json().then((response) => {
                let products = response;
                for (let p of products) {
                    addProduct(p, category_name);
                }
                let loader = document.querySelectorAll(".loader");

                for (let l of loader) {
                    l.remove();
                }
            }, onError);
        }
    }, onError);
}

function submitSearch(){
    let value = document.querySelector("#search_bar").value;
    console.log("Searchbar value: "+value);
    window.location.href = "search.php?action=search&filter="+value;
}

function searchProduct(filter) {
    console.log("filter selected:" + filter);
    fetch("../api/item_handler.php?action=search&filter=" + filter).then((response) => {
        if (response.status === 200) {
            response.json().then((response) => {
                addSection("Search", "Top results - ");
                document.querySelector("#Search-container").classList.remove("product-container");
                document.querySelector("#Search-container").classList.add("products_showing");
                let products = response;
                for (let p of products) {
                    addProduct(p, "Search");
                }
                let loader = document.querySelectorAll(".loader");

                for (let l of loader) {
                    l.remove();
                }
            }, onError);
        }else{
            searchProduct("");
        }
    }, onError);
}

function showRandomProductsCategory_mydb() {
    fetch("../api/categories_handler.php?action=list_categories").then((response) => {
        if (response.status === 200) {
            response.json().then((response) => {
                console.log("category_db:" + JSON.stringify(response));
                let cat_n = response.length;
                let cat_x = Math.round(getRandomNumber(0, cat_n - 1));
                let category = response[cat_x];
                console.log("section chosen: " + cat_x + " category: " + category["nome"]);
                addSection(category["nome"], "Più acquistati oggi nella sezione - ");
                fetch("../api/item_handler.php?action=get_item&category=" + category["nome"]).then((response) => {
                    if (response.status === 200) {
                        response.json().then((response) => {
                            console.log("category_db:" + JSON.stringify(response));
                            let products = response;
                            for (let p of products) {
                                console.log("product db: " + JSON.stringify(p));
                                addProduct(p, category["nome"]);
                            }
                        }, onError);
                    }
                }, onError);
            }, onError);
        }
    }, onError);
}

function buildCart() {
    fetch("../api/cart_handler.php?action=list_item").then((response) => {
        if (response.status === 200) {
            response.json().then((response) => {
                console.log("RESPONSE LOAD CART: " + JSON.stringify(response))
                for (item of response) {
                    addProductToCart(item);
                }
            }, onError);
        }
    }, onError);
}

function buildFavouritesSection(){
    fetch("../api/favourite_handler.php?action=list_favourite").then((response)=>{
        if(response.status === 200){
            response.json().then((response)=>{
                addSection("Favourites", "Oggetti che osservi - ");
                for (item of response) {
                    console.log("Favourites: "+JSON.stringify(item));
                    addProduct(item, "Favourites");
                }
            }, onError);
        }else{

        }
    }, onError);
}

function paymentsPayPal(total_price) {
    const client_id = "AdLFLGg69J0r9D3v9ODG2SSPC_HOddackUHdKfpYfgcvEq7PMHyyTjOlEt6auCKLsAvIAQw2KtIrO67y";
    const client_secret = "EFAbX0uVjI3nl_jm4BqGwubXHyE0xykTytbpNG2HahtGaJ4sHVJtdScSECVpfm_BQIii9Ffq2dAIjB2K";
    console.log("Request token");
    fetch("https://api-m.sandbox.paypal.com/v1/oauth2/token", {
        body: "grant_type=client_credentials",
        method: "POST",
        headers: {
            //btoa(client_id + ":" + client_secret) utilizzato perchè paypal accetta le credenziali in base 64 come da documentazione quindi ne effettuo l'encode
            //se invio i token in modo diretto si riceve L'errore http 401 non autorizzato
            Authorization: 'Basic ' + btoa(client_id + ":" + client_secret),
            "Content-Type": "application/x-www-form-urlencoded"
        }
    }, onError).then((response) => {
        if (response.status === 200) {
            response.json().then((response) => {
                const token = response.access_token;
                console.log("paypalToken: " + JSON.stringify(response));
                console.log("paypalToken: " + JSON.stringify(token));
                console.log("Do order");
                fetch("https://api.sandbox.paypal.com/v2/checkout/orders", {
                    body: "grant_type=client_credentials",
                    method: "POST",
                    headers: {
                        //btoa(client_id + ":" + client_secret) utilizzato perchè paypal accetta le credenziali in base 64 come da documentazione quindi ne effettuo l'encode
                        //se invio i token in modo diretto si riceve L'errore http 401 non autorizzato
                        Authorization: 'Bearer ' + token,
                        "Content-Type": 'application/json'
                    },
                    body: JSON.stringify({
                        intent: 'CAPTURE',
                        purchase_units: [{
                            amount: {
                                currency_code: 'USD',
                                value: total_price
                            }
                        }],
                        application_context: {
                            return_url: 'http://127.0.0.1/UNICT/WEB_PROGRAMMING/PROGETTO_EBAY/mhw3/includes/template/acquisto_ok.php',
                            cancel_url: 'http://127.0.0.1/UNICT/WEB_PROGRAMMING/PROGETTO_EBAY/mhw3/includes/template/acquisto_error.php'
                        }
                    })
                }, onError).then((response) => {
                    console.log("Do order json");
                    console.log("order req status: " + response.status);
                    if (response.status === 201) {
                        response.json().then((response) => {
                            const order_id = response.id;
                            console.log("payments: " + JSON.stringify(response));
                            console.log("payments id: " + order_id);
                            const approvalUrl = response.links.find(link => link.rel === 'approve').href;
                            window.location.href = approvalUrl;
                        }, onError);
                    }
                }, onError);
            }, onError);
        }
    }, onError);
}

function show_overlay_pay() {
    document.querySelector("#overlay_pay").style.display = "flex";
    document.body.style.overflow = "hidden";
}

function hide_overlay_pay() {
    document.querySelector("#overlay_pay").style.display = "flex";
}

function updateQtyCartProduct(product) {
    let product_cart = document.querySelectorAll(".cart-product");
    for (let p of product_cart) {
        if (p.dataset.targetProductId == product.id) {
            console.log("updateQtyCartProduct: " + p.childNodes[1].childNodes[2].textContent);
            console.log("updateQtyCartProduct product: " + JSON.stringify(product));
            p.childNodes[1].childNodes[2].textContent = "Q.ty: " + product.qty;
        }
        //console.log("childs updateQtyCartProduct: "+p.childNodes[1].childNodes[2].textContent);
    }
}

function totalCart() {
    if (cart_products.length > 0) {
        checkArrayCart();
        show_overlay_pay();
        paymentsPayPal(total_Cart);
    } else {
        notify("Error", "Impossibile completare il pagamento, il carrello è vuoto");
    }
}