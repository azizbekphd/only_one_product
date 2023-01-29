<div class="{if $selected_section !== "only_one_product"}hidden{/if}" id="content_only_one_product">
    <div class="control-group">
        <label class="control-label" for="allow_only_single_copy">{__("only_one_product.allow_only_single_copy")}</label>
        <div class="controls">
            <input type="hidden" name="product_data[allow_only_single_copy]" value="N" />
            <input type="checkbox" name="product_data[allow_only_single_copy]" id="allow_only_single_copy" value="Y"
                {if $product_data.allow_only_single_copy === "Y" || $runtime.mode === "add"}checked="checked"{/if}>
        </div>
    </div>
</div>

