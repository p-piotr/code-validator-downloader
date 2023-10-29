function add_product_dialog()
{
	document.getElementById('product_add_dialog').showModal();
}

function add_codes_from_file_dialog()
{
	document.getElementById('code_add_from_file_dialog').showModal();
}

function close_add_product_dialog()
{
	document.getElementById('product_add_dialog').close();
}

function close_add_code_from_file_dialog()
{
	document.getElementById('code_add_from_file_dialog').close();
}

function add_package_dialog()
{
	document.getElementById('package_add_dialog').showModal();
}

function close_add_package_dialog()
{
	document.getElementById('package_add_dialog').close();
}

function add_code_dialog()
{
	document.getElementById('code_add_dialog').showModal();
}

function close_add_code_dialog()
{
	document.getElementById('code_add_dialog').close();
}

function edit_code(serial_code)
{
	document.getElementById('serial_code_input').value = document.getElementById('code_serialcode_' + serial_code).innerHTML;
	document.getElementById('package_reference_input').value = document.getElementById('code_packagereference_' + serial_code).innerHTML;
	document.getElementById('expires_at_input').value = document.getElementById('code_expiresat_' + serial_code).innerHTML;
	document.getElementById('status_input').value = document.getElementById('code_status_' + serial_code).innerHTML;
	document.getElementById('code_edit_dialog').showModal();
}

function close_edit_code_dialog()
{
	document.getElementById('code_edit_dialog').close();
}

function edit_product(product_id)
{
	document.getElementById('product_id_input').value = document.getElementById('product_productid_' + product_id).innerHTML;
	document.getElementById('product_id').innerHTML = document.getElementById('product_productid_' + product_id).innerHTML;
	document.getElementById('product_name_input').value = document.getElementById('product_productname_' + product_id).innerHTML;
	document.getElementById('file_ext_name_input').value = document.getElementById('product_fileextname_' + product_id).innerHTML;
	document.getElementById('file_url_input').value = document.getElementById('product_fileurl_' + product_id).innerHTML;
	document.getElementById('product_edit_dialog').showModal();
}

function close_edit_product_dialog()
{
	document.getElementById('product_edit_dialog').close();
}

function edit_package(package_id)
{
	document.getElementById('package_id_input').value = document.getElementById('package_packageid_' + package_id).innerHTML;
	document.getElementById('package_id').innerHTML = document.getElementById('package_packageid_' + package_id).innerHTML;
	document.getElementById('package_name_input').value = document.getElementById('package_packagename_' + package_id).innerHTML;
	document.getElementById('products_included_input').value = document.getElementById('package_productsincluded_' + package_id).innerHTML;
	document.getElementById('package_edit_dialog').showModal();
}

function close_edit_package_dialog()
{
	document.getElementById('package_edit_dialog').close();
}

function edit_code(serial_code)
{
	document.getElementById('serial_code_input').value = document.getElementById('code_serialcode_' + serial_code).innerHTML;
	document.getElementById('serial_code').innerHTML = document.getElementById('code_serialcode_' + serial_code).innerHTML;
	document.getElementById('package_reference_input').value = document.getElementById('code_packagereference_' + serial_code).innerHTML;
	document.getElementById('expires_at_input').value = document.getElementById('code_expiresat_' + serial_code).innerHTML;
	document.getElementById('status_input').value = document.getElementById('code_status_' + serial_code).innerHTML;
	document.getElementById('code_edit_dialog').showModal();
}

function close_edit_code_dialog()
{
	document.getElementById('code_edit_dialog').close();
}

function triggerSubmitEventAddCodeFromText()
{
	form = document.getElementById('add_code_from_file_confirmation_form');
	//form.dispatchEvent(new Event('submit'));
	form.requestSubmit();
}

function scrollToTop()
{
	window.scrollTo(0, 0);
}

window.addEventListener ? window.addEventListener("load", scrollToTop, false) : 
window.attachEvent && window.attachEvent("onload", scrollToTop);