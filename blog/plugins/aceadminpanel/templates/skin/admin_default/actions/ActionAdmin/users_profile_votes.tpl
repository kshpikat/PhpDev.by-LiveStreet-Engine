{$oLang->adm_user_voted_users}
<table class="admin-table">
  <tr>
   	<th align="center" width="120px">Date</th>
   	<th align="center" width="150px">User</th>
   	<th align="center">Name</th>
   	<th align="center" width="100px">Vote</th>
  </tr>

  {foreach from=$aVotes.users item=aData name=el2}
  {if $smarty.foreach.el2.iteration % 2  == 0}
  	{assign var=className value=''}
  {else}
  	{assign var=className value='colored'}
  {/if}
  <tr class="{$className}">
    <td>&nbsp;{$aData.vote_date}&nbsp;</td>
    <td>&nbsp;{$aData.user_login}&nbsp;</td>
    <td>&nbsp;{$aData.title}&nbsp;</td>
    <td class="number" {if $aData.vote_value>0}style="color:#390;"{/if}{if $aData.vote_value<0}style="color:#f00;"{/if}>
    	{$aData.vote_value}
    </td>
  </tr>
  {/foreach}

</table>
<br/>

{$oLang->adm_user_voted_blogs}
<table class="admin-table">
  <tr>
   	<th width="120px">Date</th>
   	<th width="150px">User</th>
   	<th>Blog</th>
   	<th width="100px">Vote</th>
  </tr>

  {foreach from=$aVotes.blogs item=aData name=el2}
  {if $smarty.foreach.el2.iteration % 2  == 0}
  	{assign var=className value=''}
  {else}
  	{assign var=className value='colored'}
  {/if}
  <tr class="{$className}">
    <td>&nbsp;{$aData.vote_date}&nbsp;</td>
    <td>&nbsp;{$aData.user_login}&nbsp;</td>
    <td>&nbsp;{$aData.title}&nbsp;</td>
    <td class="number" {if $aData.vote_value>0}style="color:#390;"{/if}{if $aData.vote_value<0}style="color:#f00;"{/if}>
    	{$aData.vote_value}
    </td>
  </tr>
  {/foreach}

</table>
<br/>

{$oLang->adm_user_voted_topics}
<table width="100%" cellspacing="0" class="admin-table">
  <tr>
   	<th align="center" width="120px">Date</th>
   	<th align="center" width="150px">User</th>
   	<th align="center">Topic</th>
   	<th align="center" width="100px">Vote</th>
  </tr>

  {foreach from=$aVotes.topics item=aData name=el2}
  {if $smarty.foreach.el2.iteration % 2  == 0}
  	{assign var=className value=''}
  {else}
  	{assign var=className value='colored'}
  {/if}
  <tr class="{$className}">
    <td>&nbsp;{$aData.vote_date}&nbsp;</td>
    <td>&nbsp;{$aData.user_login}&nbsp;</td>
    <td>&nbsp;{$aData.title}&nbsp;</td>
    <td class="number" {if $aData.vote_value>0}style="color:#390;"{/if}{if $aData.vote_value<0}style="color:#f00;"{/if}>
    	{$aData.vote_value}
    </td>
  </tr>
  {/foreach}

</table>
<br/>

{$oLang->adm_user_voted_comments}
<table class="admin-table">
  <tr>
   	<th align="center" width="120px">Date</th>    	
   	<th align="center" width="150px">User</th>    	
   	<th align="center">Comments</th>    	
   	<th align="center" width="100px">Vote</th>    	
  </tr>
 
  {foreach from=$aVotes.comments item=aData name=el2}    
  {if $smarty.foreach.el2.iteration % 2  == 0}
  	{assign var=className value=''}
  {else}
  	{assign var=className value='colored'}
  {/if}
  <tr class="{$className}">  
    <td>&nbsp;{$aData.vote_date}&nbsp;</td>
    <td>&nbsp;{$aData.user_login}&nbsp;</td>
    <td>&nbsp;{$aData.title}&nbsp;</td>   
    <td class="number" {if $aData.vote_value>0}style="color:#390;"{/if}{if $aData.vote_value<0}style="color:#f00;"{/if}>
    	{$aData.vote_value}
    </td>   
  </tr>
  {/foreach}
  
</table>
