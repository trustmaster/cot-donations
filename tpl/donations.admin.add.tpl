<!-- BEGIN: MAIN -->
{FILE "{PHP.cfg.plugins_dir}/donations/tpl/donations.header.tpl"}
<!-- BEGIN: DONATIONS_BODY -->

<div id="main">
<!-- BEGIN: DONATIONS_ERROR-->
<div class="error">
	{DONATIONS_ERROR_BODY}
</div>
<!-- END: DONATIONS_ERROR-->
<table class="cells" width="100%" border="0" cellspacing="0" cellpadding="4">
	<tr>
		<td>
			<form action="{DONATION_ADMIN_SEND}" method="post">
			<table class="cells">
				<tr>
					<td>{PHP.L.User}</td>
					<td>{DONATION_ADMIN_USER}</td>
				</tr>
				<tr>
					<td>{PHP.L.Name}</td>
					<td>{DONATION_ADMIN_FIRSTNAME} {DONATION_ADMIN_LASTNAME}</td>
				</tr>
				<tr>
					<td>{PHP.L.donations_admin_amount}</td>
					<td>{DONATION_ADMIN_AMOUNT}</td>
				</tr>
				<tr>
					<td>{PHP.L.Email}</td>
					<td>{DONATION_ADMIN_EMAIL}</td>
				</tr>
				<tr>
					<td>{PHP.L.Date}</td>
					<td>{DONATION_ADMIN_DATE}</td>
				</tr>
				<tr>
					<td>{PHP.L.donations_admin_txnid}</td>
					<td>{DONATION_ADMIN_TXNID}</td>
				</tr>
				<tr>
					<td>{PHP.L.Status}</td>
					<td>{DONATION_ADMIN_STATUS}</td>
				</tr>
				<tr>
					<td colspan="2"><input type="submit" class="submit" name="submit" value="{PHP.L.Submit}" /></td>
				</tr>
			</table>
			</form>
		</td>
	</tr>
</table>
</div>
<!-- END: DONATIONS_BODY -->
{FILE "{PHP.cfg.plugins_dir}/donations/tpl/donations.footer.tpl"}
<!-- END: MAIN -->