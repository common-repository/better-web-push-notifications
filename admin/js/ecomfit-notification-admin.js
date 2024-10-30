(function ($) {
    function ecomfitNotificationLoginParent(webId, token, meteorToken) {
        var $form = document.createElement("form");
        var webIdElement = document.createElement("input");
        var tokenElement = document.createElement("input");
        var meteorTokenElement = document.createElement("input");

        $form.method = "POST";
        $form.action = window.location.href;

        webIdElement.value = webId;
        webIdElement.name = 'webId';
        webIdElement.type = 'hidden';
        $form.appendChild(webIdElement);

        tokenElement.value = token;
        tokenElement.name = 'token';
        tokenElement.type = 'hidden';
        $form.appendChild(tokenElement);

        meteorTokenElement.value = meteorToken;
        meteorTokenElement.name = 'meteorToken';
        meteorTokenElement.type = 'hidden';
        $form.appendChild(meteorTokenElement);

        document.body.appendChild($form);

        $form.submit();
    }


    var ecomfit_interval;

    function ecomfitNotificationOpenChildWindow(url) {
        var child = window.open(url, 'Ratting', 'width=800,height=600,0,status=0');
        ecomfit_interval = setInterval(function () {
            child.postMessage({message: "requestResult"}, "*");
        }, 500);
    }

    window.addEventListener("message", function (event) {
        if ((event.data.message === "deliverResult") && event.data.result.status) {
            var data = event.data.result.data;
            var webId = '';
            var token = '';
            var meteorToken = '';
            if (data.webId !== undefined) {
                webId = data.webId;
            }
            if (data.token !== undefined) {
                token = data.token;
            }
            if (data.meteorToken !== undefined) {
                meteorToken = data.meteorToken;
            }
            ecomfitNotificationLoginParent(webId, token, meteorToken);
            event.source.close();
            clearInterval(ecomfit_interval);
        }
    });

    $(document).ready(function () {
        $('.ecomfit-notification-btn-login').click(function (e) {
            e.preventDefault();
            ecomfitNotificationOpenChildWindow($(this).data('url'));
        });
    });
})(jQuery);
