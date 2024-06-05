<?php
require "dbconfig.php";
session_start();
if (isset($_SESSION["logged_user_id"])) {
} else {
    header("Location: login.php");
}

?>

<!DOCTYPE html>
<html lang="it">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MHW3</title>
    <link rel="stylesheet" href="../css/mhw3.css">
    <!--Utilizzata per le icone della pagina-->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<body onload="onLoadHomePage()">
    <div id="overlay_pay" class="disabled_all">
        <div class="loader_pay"></div>
        <h3>Redirect verso il servizio di pagamento...</h3>
    </div>
    <nav id="nav-content" class="main-container">
        <!--left options-->
        <ul id="left-opt">
            <!--<li><a href="http://">Ciao <?php echo ($_SESSION["logged_user_nome"]) ?>!</a><span class="material-icons">expand_more</span></li>-->
            <li class="dropdown">Ciao <?php echo ($_SESSION["logged_user_nome"]) ?>!</a><span class="material-icons">expand_more</span></a>
                <ul id="user-opt" class="dropdown-content">
                    <li><?php echo ($_SESSION["logged_user_nome"] . " " . $_SESSION["logged_user_cognome"]) ?></li>
                    <li><?php echo ($_SESSION["logged_user_username"]) ?></li>
                    <li><a href="logout.php">esci</a></li>
                </ul>
            </li>
            <li class="desktop"><a href="http://">eBay Extra</a></li>
            <li class="desktop"><a href="http://">eBay Imperdibili</a></li>
            <li class="desktop"><a href="http://">Aiuto e contatti</a></li>
        </ul>

        <!--right options-->
        <ul id="right-opt">
            <li class="desktop"><a href="http://">Vendi</a></li>
            <li class="desktop"><a href="http://">oggetti che osservi</a><span class="material-icons">expand_more</span>
            </li>
            <li><a href="http://">il mio eBay</a><span class="material-icons">expand_more</span></li>
            <li class="desktop"><a href="http://"><span class="material-icons">notifications</span></a></li>
            <li class="dropdown"><a><span class="material-icons" id="shopping-cart-btn">shopping_cart</span></a>
                <div id="shopping-cart" class="dropdown-content cart-content">
                    <!--<div class="cart-product">
                        <img src="https://fakestoreapi.com/img/71pWzhdJNwL._AC_UL640_QL65_ML3_.jpg" alt="" srcset="">
                        <div class="col">
                            <p>Title</p>
                            <h4>EUR 544</h4>
                            <small>Q.ty: 5 </small>
                        </div>
                        <span class="material-icons">delete</span>
                    </div>-->
                    <hr id="cart-separator">
                    <div id="shopping-cart-total">
                        <ul>
                            <li>
                                <h4>Articoli:&nbsp</h4>
                                <h3 id="total_cart_items"></h3>
                            </li>
                            <li>
                                <h4>Totale:&nbsp</h4>
                                <h3 id="total_cart"></h3>
                            </li>
                        </ul>
                    </div>
                    <button id="payment_btn" onclick="totalCart()">Paga</button>
                    <button id="payment_btn_close" class="mobile" onclick="closeCartMobile()">Chiudi</button>
                </div>
            </li>
        </ul>

        <!--left options-->
        <!--<ul id="left-opt-mobile" class="mobile-nav desktop" >
            <li><a href="http://">Ciao User!</a><span class="material-icons">expand_more</span></li>
        </ul>-->

        <!--right options-->
        <!--<ul id="right-opt-mobile" class="mobile-nav">
            <li><a href="http://">il mio eBay</a><span class="material-icons">expand_more</span></li>
            <li><a><span id="shopping-cart-mobile" class="material-icons">shopping_cart</span></a></li>
        </ul>-->
    </nav>
    <span class="divisor"></span>
    <header class="main-container">
        <img src="../img/ebay_logo.png" alt="" onclick="window.location.href = 'home.php'>
        <span class="pc"><a href="http://">scegli la categoria</a><span class="material-icons">expand_more</span></span>
        <div class="input-icon">
            <span class="material-icons" id="src_ico">search</span>
            <input type="text" name="search_bar" id="search_bar" placeholder="Cerca qualsiasi cosa">
            <select name="" id="" class="pc">
                <option value="Tutte le categorie" selected>Tutte le categorie</option>
                <option value="11450">Abbigliamento e accessori</option>
                <option value="353">Arte e antiquariato</option>
                <option value="9800">Auto, moto e altri veicoli</option>
                <option value="26395">Bellezza e salute</option>
                <option value="1305">Biglietti ed eventi</option>
                <option value="11700">Casa, arredamento e bricolage</option>
                <option value="1">Collezionismo</option>
                <option value="12576">Commercio, ufficio e industria</option>
                <option value="20710">Elettrodomestici</option>
                <option value="11232">Film e DVD</option>
                <option value="625">Fotografia e video</option>
                <option value="260">Francobolli</option>
                <option value="63">Fumetti</option>
                <option value="159912">Giardino e arredamento esterni</option>
                <option value="220">Giocattoli e modellismo</option>
                <option value="14339">Hobby creativi</option>
                <option value="2984">Infanzia e premaman</option>
                <option value="58058">Informatica</option>
                <option value="267">Libri e riviste</option>
                <option value="11116">Monete e banconote</option>
                <option value="11233">Musica, CD e vinili</option>
                <option value="1293">Nautica e imbarcazioni</option>
                <option value="281">Orologi e gioielli</option>
                <option value="888">Sport e viaggi</option>
                <option value="619">Strumenti musicali</option>
                <option value="15032">Telefonia fissa e mobile</option>
                <option value="293">TV, audio e video</option>
                <option value="131090">Veicoli: ricambi e accessori</option>
                <option value="1249">Videogiochi e console</option>
                <option value="62682">Vini, caffè e gastronomia</option>
                <option value="99">Altre categorie</option>
            </select>
        </div>
        <button type="button" id="search_btn"> Cerca</button>
        <a href="http://" class="pc">Avanzata</a>
    </header>
    <span class="divisor"></span>
    <div id="main-content" class="main-container">
        <ul id="sub-nav">
            <li><a href="">Home</a></li>
            <li><a href="http://">Gaming</a></li>
            <li><a href="http://">Elettrodomestici</a></li>
            <li><a href="http://">Casa e Giardino</a></li>
            <li><a href="http://">Sport</a></li>
            <li><a href="http://">Motori</a></li>
            <li><a href="http://">Ricondizionato</a></li>
            <li><a href="http://">Aste di beneficenza</a></li>
            <li style="border-left: 1px solid #000;"></li>
        </ul>
        <div id="slider">
            <img id="slider_img" src="../img/1.png" alt="" srcset="">
            <span id="back" data-target-action="back" class="material-icons slider_cmd">arrow_back_ios</span>
            <span id="forward" data-target-action="forward" class="material-icons slider_cmd">arrow_forward_ios</span>
        </div>
        <section id="brand-section">
            <h2>I brand più ricercati su eBay<span class="material-icons collapse" data-target-id="#brand-container">expand_more</span></h2>
            <div id="brand-container">
                <div class="brand">
                    <div class="brand-round">
                        <img src="../img/apple.png" alt="">
                    </div>
                    <h4>Apple</h4>
                </div>

                <div class="brand">
                    <div class="brand-round">
                        <img src="../img/dyson.png" alt="">
                    </div>
                    <h4>Dyson</h4>
                </div>

                <div class="brand">
                    <div class="brand-round">
                        <img src="../img/samsung.png" alt="">
                    </div>
                    <h4>Samsung</h4>
                </div>

                <div class="brand">
                    <div class="brand-round">
                        <img src="../img/playstation.png" alt="">
                    </div>
                    <h4>PlayStation</h4>
                </div>

                <div class="brand">
                    <div class="brand-round">
                        <img src="../img/microsoft.png" alt="">
                    </div>
                    <h4>Microsoft</h4>
                </div>

                <div class="brand">
                    <div class="brand-round">
                        <img src="../img/lego.png" alt="">
                    </div>
                    <h4>Lego</h4>
                </div>
            </div>
        </section>
        <!--<section id="watch-section">
            <h2>Oggetti che osservi<span class="material-icons collapse" data-target-id="#watch-container">expand_more</span></h2>
            <div id="watch-container">
            </div>
        </section>-->
        <section id="recent-section">
            <h2>Oggetti visti di recente<span class="material-icons collapse" data-target-id="#recent-container">expand_more</span></h2>
            <div id="recent-container">
                <div class="product-card">
                    <span class="material-icons favourite-product-card">favorite_border</span>
                    <img src="../img/motori.png" alt="" srcset="">
                    <p class="product-desc">Engine - 4 cilindri, Diesel - 1998cm3 - 155cv 110kw</p>
                    <h3 class="product-price">EUR 3490,99</h3><small>EUR 4199.99</small>
                    <!--<span class="material-icons add-to-cart">add_shopping_cart</span>-->
                </div>
                <div class="product-card">
                    <span class="material-icons favourite-product-card">favorite_border</span>
                    <img src="../img/pneumatico.png.png" alt="" srcset="">
                    <p class="product-desc">pneumatico - 4 stagioni, 255/75 R17 - 88T</p>
                    <h3 class="product-price">EUR 59,99</h3><small>EUR 119,90</small>
                    <!--<span class="material-icons add-to-cart">add_shopping_cart</span>-->
                </div>
                <div class="product-card">
                    <span class="material-icons favourite-product-card">favorite_border</span>
                    <img src="../img/pneumatico.png.png" alt="" srcset="">
                    <p class="product-desc">pneumatico - 4 stagioni, 255/75 R17 - 88T</p>
                    <h3 class="product-price">EUR 59,99</h3><small>EUR 119,90</small>
                    <!--<span class="material-icons add-to-cart">add_shopping_cart</span>-->
                </div>
                <div class="product-card">
                    <span class="material-icons favourite-product-card">favorite_border</span>
                    <img src="../img/pneumatico.png.png" alt="" srcset="">
                    <p class="product-desc">pneumatico - 4 stagioni, 255/75 R17 - 88T</p>
                    <h3 class="product-price">EUR 59,99</h3><small>EUR 119,90</small>
                    <!--<span class="material-icons add-to-cart">add_shopping_cart</span>-->
                </div>
                <div class="product-card">
                    <span class="material-icons favourite-product-card">favorite_border</span>
                    <img src="../img/pneumatico.png.png" alt="" srcset="">
                    <p class="product-desc">pneumatico - 4 stagioni, 255/75 R17 - 88T</p>
                    <h3 class="product-price">EUR 59,99</h3><small>EUR 119,90</small>
                    <!--<span class="material-icons add-to-cart">add_shopping_cart</span>-->
                </div>
                <div class="product-card">
                    <span class="material-icons favourite-product-card">favorite_border</span>
                    <img src="../img/motori.png" alt="" srcset="">
                    <p class="product-desc">Engine - 4 cilindri, Diesel - 1998cm3 - 155cv 110kw</p>
                    <h3 class="product-price">EUR 3490,99</h3><small>EUR 4199.99</small>
                    <!--<span class="material-icons add-to-cart">add_shopping_cart</span>-->
                </div>
            </div>
        </section>
        <section id="passioni-section">
            <h2>Il marketplace delle passioni<span class="material-icons collapse" data-target-id="#passioni-container">expand_more</span></h2>
            <div id="passioni-container">
                <div class="passioni">
                    <div class="passioni-round" style="background-color: blueviolet;">
                        <img src="../img/elettronica.png" alt="">
                    </div>
                    <h4>Elettronica</h4>
                </div>

                <div class="passioni">
                    <div class="passioni-round" style="background-color: orange;">
                        <img src="../img/elettrodomestici.png" alt="">
                    </div>
                    <h4>Elettrodomestici</h4>
                </div>

                <div class="passioni">
                    <div class="passioni-round" style="background-color: yellowgreen;">
                        <img src="../img/casa.png" alt="">
                    </div>
                    <h4>Casa e giardiono</h4>
                </div>

                <div class="passioni">
                    <div class="passioni-round" style="background-color: red;">
                        <img src="../img/motori.png" alt="">
                    </div>
                    <h4>Motori</h4>
                </div>

                <div class="passioni">
                    <div class="passioni-round" style="background-color: rgba(138, 138, 0, 0.699);">
                        <img src="../img/collezionismo.png" alt="">
                    </div>
                    <h4>Collezionismo</h4>
                </div>

                <div class="passioni">
                    <div class="passioni-round" style="background-color: pink;">
                        <img src="../img/moda.png" alt="">
                    </div>
                    <h4>Moda</h4>
                </div>
            </div>
        </section>
    </div>
    <footer id="footer-content">
        <div id="footer-container" class="main-container flex-row">
            <div class="col">
                <ul>
                    <li>
                        <h5>Compra</h5>
                    </li>
                    <li><a href="http://">Come fare acquisti</a></li>
                    <li><a href="http://">Acquisti per categoria</a></li>
                    <li><a href="http://">eBay Imperdibili</a></li>
                    <li><a href="http://">App eBay</a></li>
                    <li><a href="http://">I brand in vendita su eBay</a></li>
                    <li><a href="http://">Marche auto</a></li>
                    <li><a href="http://">Aste di beneficenza</a></li>
                    <li><a href="http://">Negozi Hub</a></li>
                    <li><a href="http://">eBay Extra</a></li>
                </ul>
            </div>
            <div class="col">
                <ul>
                    <li>
                        <h5>Vendi su eBay
                        </h5>
                    </li>
                    <li><a href="http://">Spazio venditori</a></li>
                    <li><a href="http://">Tariffe venditori</a></li>
                    <li><a href="http://">Negozi</a></li>
                    <li><a href="http://">Centro spedizioni</a></li>
                    <li><a href="http://">Protezione venditori</a></li>
                    <li><a href="http://">Vendite internazionali</a></li>
                    <li><a href="http://">Novità per i venditori professionali</a></li>
                    <li><a href="http://">Strumenti di vendita</a></li>
                </ul>
            </div>
            <div class="col">
                <ul>
                    <li>
                        <h5>A proposito di eBay</h5>
                    </li>
                    <li><a href="http://">Informazioni – Note legali</a></li>
                    <li><a href="http://">Mediazione</a></li>
                    <li><a href="http://">Ufficio stampa</a></li>
                    <li><a href="http://">Pubblicità su eBay</a></li>
                    <li><a href="http://">Affiliazione</a></li>
                    <li><a href="http://">Lavora in eBay</a></li>
                    <li><a href="http://">VeRO: Proprietà Intellettuale</a></li>
                </ul>
            </div>
            <div class="col">
                <ul>
                    <li>
                        <h5>Aiuto e contatti</h5>
                    </li>
                    <li><a href="http://">Spazio sicurezza</a></li>
                    <li><a href="http://">Garanzia cliente eBay</a></li>
                    <li>
                        <h5>Community</h5>
                    </li>
                    <li><a href="http://">Facebook</a></li>
                    <li><a href="http://">YouTube</a></li>
                    <li><a href="http://">Instagram</a></li>
                    <li><a href="http://">Domande e risposte tra utenti</a></li>
                    <li><a href="http://">Gruppi</a></li>
                    <li><a href="http://">Bacheca Annunci</a></li>
                </ul>
            </div>
        </div>
        <section id="disclaimer">
            <small>La riproduzione del sito è stata effettuata a scopo didattico</small>
        </section>
    </footer>
    <div id="support_container" class="helper-container">
        <div class="helper-chat">
            <h3>Benvenuto nel servizio clienti</h3>
            <p>come possiamo aiutarti?</p>
            <div class="chat-zone">
                <p>chat</p>
            </div>
            <input type="text" name="" id=""><button><span class="material-icons">send</span></button>
        </div>
    </div>
    <span class="help material-icons" id="support_btn" data-value="0">support_agent</span>
    <script src="../js/mhw3.js"></script>
</body>

</html>