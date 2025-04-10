var payment; 

$(document).ready(function(){
    $("#credit_amount").on('input', function(){
        const amount = $(this).val();
        if(amount < 5)
        {
            $("#error_credits").show();
        }else
        {
            $("#error_credits").hide();
        }
        $("#credits_total").html(amount * DATAJSON.credit_price);
    });

    $("#payment_methods_credits").on("click", "div.metodo", function(e)
    {
        const metodo = $(this).attr("data-metodo");
        Payment.buyCredits(metodo);
    });
});

function openPayment(idad = '') 
{
    $("#payment_idad").val(idad);
    $("dialog.dialog").attr("open",false);
    setStep(1);
    document.querySelector('dialog.payment').show();
}

function openMasivePayment()
{
    const ids = getSelectedItems();
    if(ids.length == 0)
    {
        alert("No has seleccionado ningún anuncio");
        return;
    }
    $("dialog.dialog").attr("open",false);
    setStep(1);
    document.querySelector('.payment').show();
    $("#payment_idad").val(JSON.stringify(ids));
}

function closePayment(){
    document.querySelector('dialog.payment').close();
    $("#payment_form")[0].reset();
    $(".delete-pending-container").hide();
}

function setStep(step)
{
    $("#payment section").hide();
    $("#step-" + step).show();
}

function orderPayment(order)
{
    payment = new Payment("", order);
}

function createPayment(nuevo = false)
{
    if(nuevo)
        payment = new Payment("payment_form", -1);
    else
        payment = new Payment("payment_form");
}

function buyCredits()
{
    $("dialog.dialog").attr("open",false);
    setStep(5);
    document.querySelector('.payment').show();
    $("#payment_methods_credits").show();
    $("#paypal_container_credits").hide();
}

function scrollPaymentTable()
{
    const contenedor = document.getElementById('payment_table');
    contenedor.scrollTo({
        left: contenedor.scrollWidth,
        behavior: 'smooth' // Desplazamiento suave
    });
}

function setExtras(anuns, monto)
{
    $("#dialog_discount").attr("open",false);
    Payment.buyExtraAds(anuns, monto);

}

function pDiscount()
{
    const sr = $("#form_discount").serializeArray();
    if(sr.length == 0)
    {
        alert("Selecciona un descuento");
        return;
    }
    $("#dialog_discount").attr("open",false);
    const descuento = sr[0].value;
    if(descuento == "5")
        setExtras(5, 10);
    if(descuento == "10")
        setExtras(10, 18);

}

function openRegistred()
{
    $("#dialog_registred").attr("open",true);
}

function openDiscount()
{
    $("#dialog_discount").attr("open",true);
}

function activarAnuncio(id)
{
    $("#limits_text").html("Activa tu anuncio o renuevalo.");
    $("#dialog_limits").attr("open", true);
    $("#limits_payment_buttom")[0].onclick = () =>
        {
            $("#dialog_limits").attr("open", false);
            openPayment(id);
        }
    $("#limits_ren_premium")[0].onclick = () =>
        {
            renovate(id, true);
        }

}

function openPending(id)
{
    openPayment();
    setStep(4);

    $.get(site_url + "sc-includes/php/ajax/" + "get_pending.ajax.php", { id: id } , function(res)
    {
        let details = "";
        if(res.discount != 0)
            {
                details = `<h3> Total: <b>${res.precio.toFixed(2)}€</b></h3>
                <p class="discount">Total con descuento de <b>${res.discount * 100}%</b>:</p>
                <p class="total"><span>${res.total.toFixed(2)}</span>€</p>
                <p class="services" >${res.details}</p>
                <p class="info">
                    Este servicio no está habilitado para
                    empresas/autónomos de Ceuta y Melilla, ni tampoco fuera de España.
                </p>
        `;
            }else
                details = `<h3> Total:</h3>
                            <p class="total"><span>${res.total}</span>€</p>
                            <p class="services" >${res.details}</p>
                            <p class="info">
                                Este servicio no está habilitado para
                                empresas/autónomos de Ceuta y Melilla, ni tampoco fuera de España.
                            </p>
                    `;
        
        $("#step-4 .payment-details").html(details);
        $(".pending").hide();

        $(".delete-pending-container").show();
        $("#delete_pending").on("click", function(){
            deletePending(id);
        });
        
        if(res.method == "transferencia")
        {
            const container = $(".pending#transferencia");
            container.show();
            container.find(".number").html(res.number);
        }
        if(res.method == "bizum")
        {
            const container = $(".pending#bizum");
            container.show();
            container.find(".number").html(res.number);
            //container.find(".amount").html(this.monto);
        }
    }, 'json');

}

class Payment
{
    constructor(form = '' ,  pedido = 0)
    {
        this.metodo = "paypal";
        this.pedido = 0;
        this.masivo = 0;
        this.ajax_url = site_url + "sc-includes/php/ajax/";

        if(form.length > 0)
        {
            if($("#payment_idad").length > 0)
                this.idad = $("#payment_idad").val();
            else if(pedido == -1)
                this.pedido = 1;
            else
                return;
                
            const self = this;
            $.get( this.ajax_url + "get_plans.ajax.php", $(`#${form}`).serialize() , function(res)
            {
                console.log(res);
                if(res.status == "success")
                {
                    if(/\[/i.test(self.idad))
                    {
                        self.idad = JSON.parse(self.idad);
                        if(self.idad.length == 1)
                        {
                            self.idad = self.idad[0];
                        }else
                        {
                            self.masivo = self.idad.length;
                        }
                    }

                    if(self.masivo)
                        self.aplyDiscount(res.monto * self.masivo);
                    else
                        self.aplyDiscount (res.monto);

                    self.planes = res.planes;
                    if(res.planes.length == 0)
                    {
                        alert("No has seleccionado ningun plan de pago");
                        return;
                    }
                    if(self.pedido)
                        self.savePedido();
                    else
                        self.displayOptions();
                }
            }, "json");

        }else if(pedido > 0)
        {
            this.execPedido(pedido);
        }

    }

    execPedido(pedido)    
    {
        const self = this;
        $.get(this.ajax_url + "get_order.ajax.php", { id: pedido } , function(res)
        {
            console.log(res);
            if(res.status == "success")
            {
                self.aplyDiscount (res.precio);
                self.planes = res.planes;
                self.pedido = pedido;
                self.idad = res.idad;
                self.displayOptions();
                document.querySelector('.payment').show();
            }
        },'json');
    }

    completePedido() 
    {
        const data =
        {
            idad: this.idad,
            metodo: this.metodo,
            pedido: this.pedido,
        };
        if(this.paypalID)
            data.paypal = this.paypalID;

        return new Promise((resolve, reject) => {
            $.post(this.ajax_url +"complete_order.ajax.php", data, function(res)
            {
                console.log(res);
                if(res.status == "success")
                {
                    resolve(res);
                }else
                {
                    reject(res);
                }
            },  "json");
        });      

    }

    procesPedido()
    {
        const self = this;
        switch (this.metodo) {
            case "creditos":
                this.completePedido().then((r) =>{
                    Payment.successMsg(r.msg);
                
                }).catch((r) =>{
                    alert(r.msg);
                });
                break;
            case "transferencia":
            case "bizum":
                this.completePedido().then((r) =>
                {
                    self.displayPending(r.number);
                });
                break;
            case "paypal":
                this.paypal().then((d) =>
                {
                    this.paypalID = d.id;
                    this.completePedido().then((r) =>
                    {
                        Payment.successMsg(r.msg);
                    });
                    console.log("pago exitoso");
                });
                break;
            default:
                break;
        }
    }

    savePedido()
    {
        const self = this;
        const data =
        {
            monto: self.monto,
            planes: self.planes.map(p => p.ID_plan),
        }
        if($("#email").length > 0)
        {
           data.email = $("#email").val();
        }

        $.post(this.ajax_url + "save_order.ajax.php", data , function(res)
        {
   
            if(res.status == "success")
            {
                console.log(res)
                $("#new_order").val(res.order);
                closePayment();
            }
        },'json');
    }

    displayOptions()
    {
        const self = this;
        setStep(2);
        
        let services = "";
        for(let i = 0; i < this.planes.length; i++)
        {
            if(i > 0)
                services += " | ";
            services += this.planes[i].name + " (" + this.planes[i].days + " días) ";
        }
        this.services = services;
       $("#step-2 .payment-details").html(this.createDetails());

       if(this.masivo > 1)
       {
           $('#payment_methods .metodo[data-metodo="bizum"]').hide();
       }else
       {
            $('#payment_methods .metodo[data-metodo="bizum"]').show();
       }
        
        $("#payment_methods").on("click", "div.metodo", function(e)
        {
            self.metodo = $(this).attr("data-metodo");
            if(self.pedido != 0)
                self.procesPedido();
            else
                self.processPayment();
        });
        
       
    }

    aplyDiscount(amount)
    {
        this.discount = null;
        this.monto = amount;

        if(amount > 100)
        {
            this.discount = {
                pre : amount,
                discount : 20
            };
            this.monto = amount - amount * 0.2;
        }

        if(amount > 300)
        {
            this.discount = {
                pre : amount,
                discount : 30
            };
            this.monto = amount - amount * 0.3;
            this.discount = true;
        }

    }

    processPayment()
    {
        switch (this.metodo) {
            case "paypal":
                $("#step-3 .payment-details").html(this.createDetails());
                this.paypal().then((d) =>
                {
                    this.paypalID = d.id;
                    this.completePayment();
                    console.log("pago exitoso");
                });
                break;
            case "creditos":
                this.completePayment();
                break;
            case "transferencia":
            case "bizum":
                if(this.masivo)
                    return;
                this.transferencia();
                break;
            default:
                break;
        }
    }
    transferencia()
    {
        const self = this;
        const data =
        {
            idad: this.idad,
            metodo: this.metodo,
            monto: this.monto,
        };

        data.planes = this.planes.map(p => p.ID_plan);

        $.post( this.ajax_url + "save_order.ajax.php", data , function(res)
        {
            console.log(res);
            if(res.status == "success")
            {
                self.displayPending(res.number);
            }
        },  "json");
   
    }

    displayPending(number)
    {
        setStep(4);
        $("#step-4 .payment-details").html(this.createDetails());
        $(".pending").hide();
        if(this.metodo == "transferencia")
        {
            const container = $(".pending#transferencia");
            container.show();
            container.find(".number").html(number);
            //container.find(".amount").html(this.monto);
        }

        if(this.metodo == "bizum")
        {
            const container = $(".pending#bizum");
            container.show();
            container.find(".number").html(number);
            //container.find(".amount").html(this.monto);
        }
    }

    createDetails()
    {
        if(this.discount)
        {
            return `<h3> Total: <b>${this.discount.pre.toFixed(2)}€</b></h3>
            <p class="discount">Total con descuento de <b>${this.discount.discount}%</b>:</p>
            <p class="total"><span>${this.monto.toFixed(2)}</span>€</p>
            <p class="services" >${this.services}</p>
            <p class="info">
                Este servicio no está habilitado para
                empresas/autónomos de Ceuta y Melilla, ni tampoco fuera de España.
            </p>
    `;
        }else
            return `<h3> Total:</h3>
                        <p class="total"><span>${this.monto}</span>€</p>
                        <p class="services" >${this.services}</p>
                        <p class="info">
                            Este servicio no está habilitado para
                            empresas/autónomos de Ceuta y Melilla, ni tampoco fuera de España.
                        </p>
                `;
    }
    creditos()
    {
        const data =
        {
            id : this.idad,
            plan: this.planes[0].ID_plan
        };

        $.post(this.ajax_url + "servicios.ajax.php", data, function(res)
        {
            console.log(res);
            closePayment();
            if(res.status == "success")
            {
                message("Se ha activado el servicio correctamente", true);
            }else if(res.status == "reject")
            {
                alert(res.msg);
            }
        },  "json");
        
    }

    static s_paypal(monto, container)
    {
        return new Promise((resolve, reject) => {
            container.html("");
            paypal.Buttons({
                style: {
                  layout: 'vertical',
                  color:  'blue',
                  shape:  'rect',
                  label:  'pay'
                },
                createOrder: function(data, actions) {
                    // This function sets up the details of the transaction, including the amount and line item details.
                    return actions.order.create({
                      purchase_units: [{
                        amount: {
                          value: monto
                        }
                      }]
                    });
                  },
                  onApprove: function(data, actions) {
                    // This function captures the funds from the transaction.
                    return actions.order.capture().then(function(details) {
                      // This function shows a transaction success message to your buyer.
                      console.log(details);
                      if(details.status == "COMPLETED")
                      {
                        //createPay(details.id)
                        resolve(details);
                      }else
                      {
                        reject(details);
                      }
                    });
                  },
                  onCancel: function(data)
                  {
                    console.log(data);

                  }
              }).render(container[0]);
        });
    }

    paypal()
    {
        const self = this;
        $("#step-3 .payment-details").html(this.createDetails());
        setStep(3);
        return new Promise((resolve, reject) => {
            const container = $("#paypal_module");
            container.html("");
            paypal.Buttons({
                style: {
                  layout: 'vertical',
                  color:  'blue',
                  shape:  'rect',
                  label:  'pay'
                },
                createOrder: function(data, actions) {
                    // This function sets up the details of the transaction, including the amount and line item details.
                    return actions.order.create({
                      purchase_units: [{
                        amount: {
                          value: self.monto
                        }
                      }]
                    });
                  },
                  onApprove: function(data, actions) {
                    // This function captures the funds from the transaction.
                    return actions.order.capture().then(function(details) {
                      // This function shows a transaction success message to your buyer.
                      console.log(details);
                      if(details.status == "COMPLETED")
                      {
                        //createPay(details.id)
                        resolve(details);
                      }else
                      {
                        reject(details);
                      }
                    });
                  },
                  onCancel: function(data)
                  {
                    console.log(data);

                  }
              }).render(container[0]);
        });
    }

    completePayment()
    {
        const data =
        {
            idad: this.idad,
            metodo: this.metodo,
            monto: this.monto,
            masivo: this.masivo,
        };

        if(this.paypalID)
            data.paypalID = this.paypalID;

        data.planes = this.planes.map(p => p.ID_plan);
        $.post( this.ajax_url + "payment.ajax.php", data, function(res)
        {
            console.log(res);
            if(res.status == "success")
            {
                
                Payment.successMsg(res.msg);
            }else
            {
                alert(res.msg);
            }
        },  "json");
    }

    //creditos
    static buyCredits(metodo)
    {
        const amount = $("#credit_amount").val();
        if(amount < 10)
        {
            $("#error_credits").show();
            return;
        }

        if(metodo == "paypal")
        {
            $("#payment_methods_credits").hide();
            $("#paypal_container_credits").show();
            Payment.s_paypal(amount * DATAJSON.credit_price, $("#paypal_container_credits"))
            .then((r) =>
            {
                Payment.callBuyCredits(metodo, amount, r.id);
            });
        }else
        {
            Payment.callBuyCredits(metodo, amount);
        }
    }

    static callBuyCredits(metodo, amount, paypalID = "")
    {
        const data =
        {
            metodo: metodo,
            cantidad: amount,
            paypalID: paypalID
        };
        $.post(site_url + "sc-includes/php/ajax/buy_credits.php", data, function(res)
        {
            console.log(res);
            if(res.status == "success")
            {
                $("#credit_amount").val(0);
                $("#error_credits").hide();
                //Payment.successMsg("Créditos agregados correctamente");
                $(".info_valid").html("Créditos agregados correctamente");
                $(".info_valid").show();
            }
            if(res.status == "pending")
            {
                openPayment();
                setStep(4);
                const number = res.number;
                $("#step-4 .payment-details").html(`<h3> Total:</h3>
                        <p class="total"><span>${amount * DATAJSON.credit_price}</span>€</p>
                        <p class="services" >Créditos</p>
                        <p class="info">
                            Este servicio no está habilitado para
                            empresas/autónomos de Ceuta y Melilla, ni tampoco fuera de España.
                        </p>
                `);
               
                $(".pending").hide();
                if(metodo == "transferencia")
                {
                    const container = $(".pending#transferencia");
                    container.show();
                    container.find(".number").html(number);
                    //container.find(".amount").html(this.monto);
                }
        
                if(metodo == "bizum")
                {
                    const container = $(".pending#bizum");
                    container.show();
                    container.find(".number").html(number);
                    //container.find(".amount").html(this.monto);
                }


            }
        },'json');
    }

    // Anuncios extras

    static buyExtraAds(num, monto)
    {
        openPayment();
        setStep(6);
        $("#extra_anun_services").html(`${num} anuncios`);
        $("#extra_anun_price").html(monto);

        $("#payment_methods_extras .metodo").click(function(){
          
            const metodo = $(this).data('metodo');
            $("#payment_methods_extras").show();
            $("#paypal_container_extras").hide();
     
            if(metodo == "creditos")
                Payment.callBuyExtras(metodo, num);

            if(metodo == "paypal")
            {
                $("#payment_methods_extras").hide();
                $("#paypal_container_extras").show();
                Payment.s_paypal(monto, $("#paypal_container_extras")).then( r =>
                {
                    Payment.callBuyExtras(metodo, num, r.id);
                });
            }

        });

    }

    static callBuyExtras(metodo, amount, paypalID = "")
    {
        const data =
        {
            metodo: metodo,
            p: amount,
            paypalID: paypalID
        };


        $.post(site_url + "sc-includes/php/ajax/buy_package.ajax.php", data, function(res)
        {
            console.log(res);
            if(res.status == "success")
            {
                //Payment.successMsg(res.msg);
                location.href = location.href + "?buyExtras=1";
            }else
            {
                //Payment.errorMsg(res.msg);
                alert(res.msg);
                openPayment();
            }
        }, "json");
    }

    static successMsg(msg = "")
    {
        closePayment();
        window.location.href = site_url + "mis-anuncios/?paysuccess=" + msg;
    }

    static errorMsg(msg = "")
    {
        closePayment();
        window.location.href = site_url + "mis-anuncios/?payerror=" + msg;
    }
}   