{if !empty($featuredPages)}
    <h2>Páginas destacadas</h2>
    {foreach from=$featuredPages item=page}
        <p>
            <a href="{$link->getCMSLink($page.id)|escape:'html'}">
                {(empty($page.linkText)) ? $page.title : $page.linkText}
            </a>
        </p>
    {/foreach}
{/if}
