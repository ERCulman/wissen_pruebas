$(document).ready(function() {

    // Smooth scrolling para el menú de navegación
    $('a.page-scroll').bind('click', function(event) {
        var $anchor = $(this);
        $('html, body').stop().animate({
            scrollTop: ($($anchor.attr('href')).offset().top - 50) // -50 para compensar la altura del navbar
        }, 1250, 'easeInOutExpo');
        event.preventDefault();
    });

    // Función para animar contadores cuando son visibles
    function animateCounterOnScroll() {
        var oTop = $('#stats').offset().top - window.innerHeight;
        if ($(window).scrollTop() > oTop) {
            $('.count').each(function() {
                var $this = $(this),
                    countTo = $this.attr('data-count');
                $({ Counter: $this.text() }).animate({
                    Counter: countTo
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.ceil(this.Counter));
                    },
                    complete: function() {
                        $this.text(this.Counter);
                    }
                });
            });
            // Desactiva el listener una vez que la animación ha comenzado
            $(window).off("scroll", animateCounterOnScroll);
        }
    }

    // Llamada AJAX para obtener las estadísticas
    $.ajax({
        url: 'ajax/estadisticas.php',
        type: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.status === "ok") {
                $('#total-instituciones').attr('data-count', data.instituciones || 0);
                $('#total-sedes').attr('data-count', data.sedes || 0);
                $('#total-estudiantes').attr('data-count', data.estudiantes || 0);
                $('#total-docentes').attr('data-count', data.docentes || 0);
                $('#total-acudientes').attr('data-count', data.acudientes || 0);
                
                // Inicia la animación solo cuando el usuario se desplaza a la sección
                $(window).on("scroll", animateCounterOnScroll);
                animateCounterOnScroll(); // Intenta animar al cargar por si ya es visible
            } else {
                console.error("Error desde la API: ", data.message);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fatal al cargar estadísticas. Estado: " + status + ". Error: " + error);
            console.log(xhr.responseText);
        }
    });

    // Cargar jQuery Easing para el smooth scroll (opcional pero recomendado)
    // Nota: Esto debería idealmente estar en el HTML, pero se agrega aquí para asegurar la funcionalidad.
    if (typeof $.easing.easeInOutExpo === "undefined") {
        $.getScript('https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js');
    }
});