<script>
    $(document).ready(function() {ldelim}
        const urlParams = new URLSearchParams(window.location.search);
        const hasAnnotations = urlParams.get('hasAnnotations');
        if(hasAnnotations) {ldelim}
            $("#pdfCanvasContainer > iframe").on("load", function(){ldelim}
                let iframeWindow = $("#pdfCanvasContainer > iframe")[0].contentWindow;
                iframeWindow.hypothesisConfig = function () {ldelim}
                    return {ldelim}
                        "openSidebar": true
                    {rdelim};
                {rdelim};
            {rdelim});
        {rdelim}
    {rdelim});
</script>