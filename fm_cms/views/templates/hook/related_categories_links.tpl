{if !empty($relatedCategories)}
    {if !empty($relatedCategoriesTitle)}
        <h2>{$relatedCategoriesTitle}</h2>
    {else}
        <h2>Categorías relacionadas</h2>
    {/if}
    {foreach from=$relatedCategories item=cat}
        <p>
            <a href="{$link->getCMSCategoryLink($cat.id)|escape:'html'}">
                {$cat.title}
            </a>
        </p>
    {/foreach}
{/if}
