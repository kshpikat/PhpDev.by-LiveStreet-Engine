			{hook run='content_end'}
			</div><!-- /content-inner -->
		</div><!-- /content -->

		{if !$noSidebar}
			{include file='sidebar.tpl'}
		{/if}
	</div><!-- /wrapper -->

	<div id="footer">
		<div id="footer-inner">
			<div class="right">{hook run='copyright'}</div>
			Design by — <a href="http://www.xeoart.com">Студия XeoArt</a>
			<img border="0" src="{cfg name='path.static.skin'}/images/xeoart.gif">
			{if $oUserCurrent and $oUserCurrent->isAdministrator()}| <a href="{cfg name='path.root.web'}/admin">{$aLang.admin_title}</a>{/if}
			{hook run='footer_end'}
		</div>
	</div>

</div><!-- /container -->

<script type="text/javascript">
    var reformalOptions = {
        project_id: 54518,
        project_host: "phpdevby.reformal.ru",
        force_new_window: false,
        tab_alignment: "left",
        tab_top: "300",
        tab_bg_color: "#F08200",
        tab_image_url: "http://tab.reformal.ru/0JrQvdC40LPQsCDQvtGC0LfRi9Cy0L7QsiDQuCDQv9GA0LXQtNC70L7QttC10L3QuNC5/FFFFFF/dcd11cd3393a4956cb9dffe891d9112e"
    };
    
    (function() {
        var script = document.createElement('script');
        script.type = 'text/javascript'; script.async = true;
        script.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'media.reformal.ru/widgets/v1/reformal.js?1';
        document.getElementsByTagName('head')[0].appendChild(script);
    })();
</script>

{hook run='body_end'}

</body>
</html>