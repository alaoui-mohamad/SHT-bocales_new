<div id="top">
            <div class="logo"></div>
            <div class="info">
                <h2><%= order_number_format %></h2>
            </div>
         <img src="https://firiandco.com/wp-content/uploads/2021/10/firico-market-logo-e1637701425846.png">
</div>
<div id="mid">
            <div class="info">
                <p>Note: <%= note %></p>
                <p>Date: <%= created_at %></p>
                <p>Nom du client: <%= customer.name %></p>
                <p>Téléphone client : <%= customer.phone %></p>
                <p>Email client : <%= customer.email %></p>
                <p>Adresse client : <%= customer.address %></p>
                <p>Ville du client : <%= customer.city %></p>
                <p>Code postal du client : <%= customer.postcode %></p>
                <p>Pays du client : <%= customer.country %></p>
                <p>Caissier   : <% if(typeof sale_person_name != "undefined") { %> <%= sale_person_name %> <% } %> </p>
            </div>
            <div class="payment-details">
                <ul class="payment-methods">
                    <% payment_method.forEach(function(payment){ %>
                        <li><%= payment.name %> : <%= payment.paid %></li>
                        <% if (payment.code == "cash" && payment.return > 0) { %>
                            <li>return: <%= payment.return %></li>
                        <% } %>
                    <% }); %>
                </ul>
            </div>
            <% if(add_shipping){ %>
                <div class="shipping-details">
                    <p>Shipping method: <%- shipping_information.shipping_method_details.label %></p>
                    <p>Shipping name: <%- shipping_information.name %></p>
                    <p>Shipping address: <%- shipping_information.address %></p>
                    <p>Shipping address 2: <%- shipping_information.address_2 %></p>
                    <p>Shipping city: <%- shipping_information.city %></p>
                    <p>Shipping postcode: <%- shipping_information.postcode %></p>
                    <p>Shipping state: <%- shipping_information.state %></p>
                </div>
            <% }; %>
</div>


<table>
    <tr class="tabletitle items-table-label">
        <td class="item"><h2>Produit(s)</h2></td>
        <td class="qty"><h2>Prix Unité</h2></td>
        <td class="qty"><h2>Qté</h2></td>
        <td class="total"><h2>Total</h2></td>
        <td class="total"><h2>Promo</h2></td>
        <td class="total"><h2>Taxes</h2></td>
        <td class="total"><h2>Total TTC</h2></td>
    </tr>
    <% items.forEach(function(item){ %>
    <tr class="service">
        <td class="tableitem item-name">
            <p class="itemtext"><%= item.name %></p>
            <% if(item.sub_name.length > 0){ %>

                   <p class="option-item"> <%- item.sub_name  %> </p>

            <% }; %>

        </td>
        <td class="tableitem item-price">
        <p class="itemtext">
        
       <% if( item.price_currency_formatted != item.final_price_currency_formatted ) {%>
                <span style="text-decoration: line-through;"><%= item.price_currency_formatted %></span>
        <% }; %>
        <%= item.final_price_currency_formatted %>
        </p>
        </td>
        <td class="tableitem item-qty"><p class="itemtext"><%= item.qty %></p></td>

        <td class="tableitem item-total"><p class="itemtext"><%= item.total_currency_formatted %></p></td>
        <td class="tableitem item-total"><p class="itemtext"><%= item.final_discount_amount_currency_formatted %></p></td>
        <td class="tableitem item-total"><p class="itemtext"><%= item.tax_amount_currency_formatted %></p></td> 
        <td class="tableitem item-total"><p class="itemtext"><%= item.product.cps_tax %></p></td>
    </tr>
    <% }); %>
    <tr class="tabletitle">

        <td class="Rate sub-total-title" style="text-align:right;padding-right:5px;"  colspan="6"><h2>Sous Total:</h2></td>
        <td class="payment sub-total-amount"><h2><%= sub_total_currency_formatted %></h2></td>
    </tr>

    <tr class="tabletitle">

        <td class="Rate shipping-title" style="text-align:right;padding-right:5px;"  colspan="6"><h2>Total Expédition:</h2></td>
        <td class="payment shipping-amount"><h2><%= shipping_cost_currency_formatted %></h2></td>
    </tr>

    <tr class="tabletitle">
            <td class="Rate tax-title" style="text-align:right;padding-right:5px;" colspan="6"><h2>Total Promo:</h2></td>
            <td class="payment cart-discount-amount"><h2><%= final_discount_amount_currency_formatted %></h2></td>
     </tr>

 <% tax_details.forEach(function(tax){ %>
            <tr class="tabletitle">
                    <td class="Rate tax-title" style="text-align:right;padding-right:5px;" colspan="6"><h2><%= tax.rate %>% - <%= tax.label %></h2></td>
                    <td class="payment tax-amount"><h2><%= tax.total %></h2></td>
                </tr>
                   
                    <% }); %>
    <tr class="tabletitle">
        <td class="Rate tax-title" style="text-align:right;padding-right:5px;" colspan="6"><h2>Total des Taxes:</h2></td>
        <td class="payment tax-amount"><h2><%= tax_amount_currency_formatted %></h2></td>
    </tr>
    <tr class="tabletitle">

        <td class="Rate grand-total-title" style="text-align:right;padding-right:5px;"  colspan="6"><h2>TOTAL TTC:</h2></td>
        <td class="payment grand-total-amount"><h2><%= grand_total_currency_formatted %></h2></td>
    </tr>
    <tr class="tabletitle">
        <td class="Rate grand-total-title" style="text-align:right;padding-right:5px;"  colspan="6"><h2>Payé par le client:</h2></td>
        <td class="payment grand-total-amount"><h2><%= customer_total_paid_currency_formatted %></h2></td>
    </tr>
    <tr class="tabletitle">
        <td class="Rate grand-total-title" style="text-align:right;padding-right:5px;"  colspan="6"><h2>Rendu:</h2></td>
        <td class="payment grand-total-amount"><h2><%= remain_paid_currency_formatted %></h2></td>
    </tr>
</table>


<div id="legalcopy">
     <p class="legal"><strong>Merci d'avoir choisi Firi&Co Market !
     <br>
     <br></strong>N°RCS 1764C | N°TAHITI : C37294
     <br>Taravao – Route de Teahupoo Centre TAUHERE - BP 8265 – Tahiti – Polynésie Française
     GROUPE CYRACH TAHITI - BANQUE DE POLYNESIE<br>
     RIB : 12149 06744 44006527907 77<br>
IBAN : FR7612149067444400652790777 BIC/SWIFT : BPOLPFTP<br><br>-
     </p>
</div>