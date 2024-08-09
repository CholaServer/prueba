{if !empty($relatedPages)}
    {if !empty($relatedPagesTitle)}
        <h2>{$relatedPagesTitle}</h2>
    {else}
        <h2>Páginas relacionadas</h2>
    {/if}
    {foreach from=$relatedPages item=page}
        <p>
            <a href="{$link->getCMSLink($page.id)|escape:'html'}">
                {$page.title}
            </a>
        </p>
    {/foreach}
{/if}
