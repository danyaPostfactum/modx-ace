<textarea id="tv{$tv->id}" name="tv{$tv->id}"  tvtype="{$tv->type}" style="width: 100%; height: 200px;">{$tv->value|escape}</textarea>

<script type="text/javascript">
    // <![CDATA[{literal}
    Ext.onReady(function(){
    	{/literal}
		MODx.ux.Ace.replaceTextArea('tv{$tv->id}',{$config});	
		{literal}
	});
    {/literal}
    // ]]>
</script>
