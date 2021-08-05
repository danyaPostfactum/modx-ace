<textarea name="tv{$tv->id}" id="tv{$tv->id}">{$tv->value}</textarea>

<script type="text/javascript">
    // <![CDATA[{literal}
    Ext.onReady(function(){
    	{/literal}
		MODx.ux.Ace.replaceTextAreas(Ext.query('#tv{$tv->id}'));		
		{literal}
	});
    {/literal}
    // ]]>
</script>
