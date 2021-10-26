<textarea id="tv{$tv->id}" name="tv{$tv->id}"  tvtype="{$tv->type}">{$tv->value|escape}</textarea>

<script type="text/javascript">
	// <![CDATA[{literal}
	Ext.onReady(function(){
		{/literal}
			MODx.ux.Ace.replaceTextArea('tv{$tv->id}');
		{literal}
	});
	{/literal}
	// ]]>
</script>
