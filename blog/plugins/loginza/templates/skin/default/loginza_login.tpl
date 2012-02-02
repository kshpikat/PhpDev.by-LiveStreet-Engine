<p>{$aLang.loginza_welcome_message}</p>
<script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>
<div style="text-align:center;">
<iframe src="https://loginza.ru/api/widget?overlay=loginza&theme=grey&providers_set={$oConfig->GetValue('plugin.loginza.widget.providers')}&lang={$oConfig->GetValue('plugin.loginza.widget.lang')}&token_url={$aRouter.login|urlencode}{'loginza/result'|urlencode}" 
style="width:359px;height:200px;" scrolling="no" frameborder="no"></iframe>
</div>
<div style="text-align:center;">
<div style="display:inline-block;width:33%;border-top:1px solid #999;height:5px;line-height:0px;"></div>
<div style="display:inline-block;font-size:14pt;color:#555;padding-bottom:30px;">{$aLang.loginza_or_message}</div>
<div style="display:inline-block;width:33%;border-top:1px solid #999;height:5px;line-height:0px;"></div>
</div>