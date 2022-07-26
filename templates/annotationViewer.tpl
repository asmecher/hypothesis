<li class="annotation_viewer_li">
    <span class="annotation_viewer_span">
        {if $annotationsNumber eq 1}    
            {$annotationsNumber} {translate key="plugins.generic.hypothesis.annotation"}
        {else}
            {$annotationsNumber} {translate key="plugins.generic.hypothesis.annotations"}
        {/if}
    </span>
</li>