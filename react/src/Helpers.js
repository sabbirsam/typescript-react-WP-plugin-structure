const config = Object.assign({}, window.swptls_APP);

export function getNonce() {
	return config.nonce;
}

export function getTables() {
	return config.tables;
}

export function getTabs() {
	return config.tabs;
}

export function isValidGoogleSheetsUrl(url) {
	// Regular expression to match Google Sheets URLs
	var pattern = /^https:\/\/docs\.google\.com\/spreadsheets\/d\/([a-zA-Z0-9_-]+)/;

	// Test if the URL matches the pattern
	return pattern.test(url);
}


// Default setting once table create.
export function getDefaultSettings() {
	return {	
		table_title: false,
		default_rows_per_page: 10,
		show_info_block: true,
		responsive_table: false,
		show_x_entries: true,
		swap_filter_inputs: false,
		swap_bottom_options: false,
		allow_sorting:false,
		search_bar: true,
		table_export: [],
		vertical_scroll: null,
		cell_format: "expand",
		responsive_style: "default_style",
		redirection_type: "_blank",
		table_cache: false,
		table_style: 'default-style',
		hide_column: [],

		hide_on_desktop:true,
		hide_on_mobile:false,

		hide_rows: [],
		hide_cell: [],
		table_styles: false,
		table_cache: false,
	};
}

export function convertToSlug(str) {
  return str.toLowerCase().replace(/\s+/g, '-');
}

export function getLicenseUrl() {
	return config.pro.license_url;
}

export function isProInstalled() {
	return config.pro.installed;
}

export function isProActive() {
	return config.pro.active;
}

export function isProLicenseActive() {
	return config.pro.license;
}

export const getSpreadsheetID = (url) => {
	if (!url || url == "") return;

	let sheetID = null;

	sheetID = url.split(/\//)[5];

	if (sheetID) return sheetID;

	return null;
}

export const getGridID = (url) => {
	if (!url || url == "") return;

	let gridID = null;

	gridID = url.match(/gid=(\w+)/);
	
	if ( ! gridID ) {
		return null;
	}

	gridID = gridID[1];

	if (gridID) return gridID;

	return null;
}

export const setPdfUrl = (url) => {
	const spreadsheetID = getSpreadsheetID(url);
	const gridID = getGridID(url);
	const pdfUrl = "https://docs.google.com/spreadsheets/d/" + spreadsheetID + "/export?format=pdf&id=" + spreadsheetID + "&gid=" + gridID;

	const createTablesWrapper = document.getElementById("create_tables_wrapper");
	const dtButtons = createTablesWrapper.getElementsByClassName("dt-buttons")[0];

	if (!dtButtons.getElementsByClassName("pdf_btn").length) {
		const pdfBtn = document.createElement("a");
		// pdfBtn.className = "ui dt-button inverted red button transition hidden pdf_btn";
		pdfBtn.className = "ui dt-button inverted button transition hidden pdf_btn";
		pdfBtn.href = pdfUrl;
		pdfBtn.download = "";

		const span = document.createElement("span");
		pdfBtn.appendChild(span);

		// PDF text add 
		// const textNode = document.createTextNode("PDF \u00A0");
		// span.appendChild(textNode);

		const img = document.createElement("img");
		img.src = swptls_APP.icons.filePdf;
		span.appendChild(img);

		dtButtons.appendChild(pdfBtn);
	}
}

export const screenSize = () => {
	// Desktop screen size
	if (screen.width > 740) {
		return "desktop";
	} else {
		return "mobile";
	}
}

// Return an array that will define the columns to hide
export const hideColumnByScreen = (arrayValues) => {

	return [
		{
			targets: arrayValues,
			visible: false,
			searchable: false,
		},
	];
};


/* export function getExportButtonOptions(values) {
	return [
		{
			text: `JSON &nbsp;<img src="${swptls_APP.iconsURL.curlyBrackets}" />`,
			className:
				'ui inverted yellow button transition hidden json_btn',
			action(e, dt, button, config) {
				const data = dt.buttons.exportData();

				$.fn.dataTable.fileSave(
					new Blob([JSON.stringify(data)]),
					`${values.table_name}.json`
				);
			},
		},
		{
			text: `CSV &nbsp;<img src="${swptls_APP.iconsURL.fileCSV}" />`,
			extend: 'csv',
			className:
				'ui inverted green button transition hidden csv_btn',
			title: `${values.table_name}`,
		},
		{
			text: `Excel &nbsp;<img src="${swptls_APP.iconsURL.fileExcel}" />`,
			extend: 'excel',
			className:
				'ui inverted green button transition hidden excel_btn',
			title: `${values.table_name}`,
		},
		{
			text: `Print &nbsp;<img src="${swptls_APP.iconsURL.printIcon}" />`,
			extend: 'print',
			className:
				'ui inverted secondary button transition hidden print_btn',
			title: `${values.table_name}`,
		},
		{
			text: `Copy &nbsp;<img src="${swptls_APP.iconsURL.copySolid}" />`,
			extend: 'copy',
			className:
				'ui inverted violet button transition hidden copy_btn',
			title: `${values.table_name}`,
		},
	]
} */


export function getExportButtonOptions(values) {
	return [
		{
			text: `<img src="${swptls_APP.iconsURL.curlyBrackets}" />`,
			className:
				'ui inverted button transition hidden json_btn',
			action(e, dt, button, config) {
				const data = dt.buttons.exportData();

				$.fn.dataTable.fileSave(
					new Blob([JSON.stringify(data)]),
					`${values.table_name}.json`
				);
			},
		},
		{
			text: `<img src="${swptls_APP.iconsURL.fileCSV}" />`,
			extend: 'csv',
			className:
				'ui inverted button transition hidden csv_btn',
			title: `${values.table_name}`,
		},
		{
			text: `<img src="${swptls_APP.iconsURL.fileExcel}" />`,
			extend: 'excel',
			className:
				'ui inverted button transition hidden excel_btn',
			title: `${values.table_name}`,
		},
		{
			text: `<img src="${swptls_APP.iconsURL.printIcon}" />`,
			extend: 'print',
			className:
				'ui inverted button transition hidden print_btn',
			title: `${values.table_name}`,
		},
		{
			text: `<img src="${swptls_APP.iconsURL.copySolid}" />`,
			extend: 'copy',
			className:
				'ui inverted button transition hidden copy_btn',
			title: `${values.table_name}`,
		},
	]
}


export const export_buttons_row_revealer = (export_btns) => {
	if (export_btns) {
		export_btns.forEach((btn) => {
			setTimeout(() => {
				export_button_revealer_by_other_input(btn);
			}, 300);
		});
	}
};

const export_button_revealer_by_other_input = (btn) => {
	const button = document.querySelector('.' + btn + '_btn');
	if (button.classList.contains('hidden')) {
		button.classList.remove('hidden');
		button.classList.add('scale');
	}
};

export const swap_bottom_options = (state) => {
	let pagination_menu = document.querySelector("#bottom_options");

	if (state) {
		pagination_menu.classList.add( 'swap' );
	} else {
		pagination_menu.classList.remove( 'swap' );
	}
}

export const swap_top_options = (state) => {
	let pagination_menu = document.querySelector("#filtering_input");

	if (state) {
		pagination_menu.classList.add( 'swap' );
	} else {
		pagination_menu.classList.remove( 'swap' );
	}
}

const bottom_option_style = ($arg) => {
	document.querySelector("#bottom_options").style.flexDirection = $arg["flex_direction"];
	document.querySelector("#create_tables_info").style.marginLeft = $arg["table_info_style"]["margin_left"];
	document.querySelector("#create_tables_info").style.marginRight = $arg["table_info_style"]["margin_right"];
	document.querySelector("#create_tables_paginate").style.marginLeft = $arg["table_paginate_style"]["margin_left"];
	document.querySelector("#create_tables_paginate").style.marginRight = $arg["table_paginate_style"]["margin_right"];
}

export const changeCellFormat = (formatStyle, tableCell) => {
	tableCell = document.querySelectorAll( tableCell );
    switch (formatStyle) {
        case "wrap":
            tableCell.forEach(cell => {
                cell.classList.remove("clip_style");
                cell.classList.remove("expanded_style");
                cell.classList.add("wrap_style");
            });
            break;

        case "clip":
            tableCell.forEach(cell => {
                cell.classList.remove("wrap_style");
                cell.classList.remove("expanded_style");
                cell.classList.add("clip_style");
            });
            break;

        case "expand":
            tableCell.forEach(cell => {
                cell.classList.remove("clip_style");
                cell.classList.remove("wrap_style");
                cell.classList.add("expanded_style");
            });
            break;

        default:
            break;
    }
};

export const displayProPopup = () => {
    WPPOOL.Popup('sheets_to_wp_table_live_sync').show();
}

export const getSetupWizardStatus = () => {
	return config.ran_setup_wizard;
}


export function show_export_buttons(buttons) {
	if (buttons) {
		buttons.forEach((btn) => {
			if (document.querySelector("." + btn + "_btn")) {
				if ( ! buttons.includes(btn) ) {
					document.querySelector("." + btn + "_btn").style = 'display: block;';
				} else {
					document.querySelector("." + btn + "_btn").style = 'display: block;';	
				}
			}
		});
	}
}

// Hide table title instant 
export function handleTableAppearance(settings) {
	if (document.getElementById('swptls-table-title')) {
		if (!settings?.show_title) {
			document.getElementById('swptls-table-title').style = 'display: none;';
		} else {
			document.getElementById('swptls-table-title').style = 'display:block;';
		}
	}

	if (document.getElementById('create_tables_filter')) {
		if (!settings?.search_bar) {
			document.getElementById('create_tables_filter').style = 'display: none;';
		} else {
			document.getElementById('create_tables_filter').style = 'display: block;';
		}
	}

	// Here Paginate 
	if (document.getElementById('create_tables_paginate')) {
		if (!settings?.pagination) {
			document.getElementById('create_tables_paginate').style = 'display: none;';
		} else {
			document.getElementById('create_tables_paginate').style = 'display: block;';
		}
	}


	if (document.getElementById('create_tables_info')) {
		if (!settings?.show_info_block) {
			document.getElementById('create_tables_info').style = 'display: none;';
		} else {
			document.getElementById('create_tables_info').style = 'display: block;';
		}
	}

	if (document.getElementById('create_tables_length')) {
		if (!settings?.show_x_entries) {
			document.getElementById('create_tables_length').style = 'display: none;';
		} else {
			document.getElementById('create_tables_length').style = 'display: block;';
		}
	}

	// Here add the code and removed from EditTable.tsx -----------------------------------------------------------------
	// Implement Row to show per page and Height live preview view.

		const selectElement2 = document.querySelector('#create_tables_length select');
		const selectElement = document.querySelector('#rows-per-page');

		const createTables = document.getElementById("create_tables");
		const table_height = document.querySelector('#table_height');
		const scrollBodyElement = document.querySelector('.dataTables_scrollBody');

		if (selectElement2) {
			if (selectElement) {
				selectElement.addEventListener('change', (event) => {
					const selectedValue = event.target.value;
					selectElement2.value = selectedValue;

					const changeEvent = new Event('change', { bubbles: true });
					selectElement2.dispatchEvent(changeEvent);
				});
			}
		}

		// Siple hide createTables part to fix table height problem later on 

		if (createTables) {
			const isWrapped = createTables.classList.contains("dataTables_scrollBody");
			if (!isWrapped) {
				const wrapperDiv = document.createElement("div");
				wrapperDiv.className = "dataTables_scrollBody";
				wrapperDiv.style.position = "relative";
				wrapperDiv.style.overflow = "auto";
				wrapperDiv.style.maxHeight = "";
				wrapperDiv.style.height = "";
				wrapperDiv.style.width = "100%";

				createTables.parentNode.insertBefore(wrapperDiv, createTables);
				wrapperDiv.appendChild(createTables);
			}
		}


		if (table_height) {
			table_height.addEventListener('change', (event) => {
				const selectedHeight = event.target.value;

				if (selectedHeight === 'default_height') {
					if (scrollBodyElement) {
						scrollBodyElement.style.removeProperty('maxHeight');
						scrollBodyElement.style.removeProperty('height');
					}
				} else {
					if (scrollBodyElement) {
						scrollBodyElement.style.maxHeight = selectedHeight + 'px';
						scrollBodyElement.style.height = selectedHeight + 'px';
					}
				}
			});
		}

	// END ----------------------------------------------------------------------------------------------

	function handleDisableSorting(e) {
		e.stopPropagation();
		window.swptlsDataTable.order([0, 'asc']).draw();
	}

	if (!settings?.allow_sorting) {

		if (document.getElementsByClassName('thead-item sorting').length) {
			const headers = document.getElementsByClassName('thead-item sorting');

			for (let item of headers) {
				item.addEventListener('click', handleDisableSorting);
			}
		}
	} else {
		if (document.getElementsByClassName('thead-item').length) {
			const headers = document.getElementsByClassName('thead-item');

			var clicked = false;

			function reloadTable() {
				if ( ! clicked ) {
					window.swptlsDataTable.order([ 0, 'desc' ]).draw();
					clicked = true;
				} else {
					window.swptlsDataTable.order([ 0, 'asc' ]).draw();
					clicked = false;
				}
			}

			for (let item of headers) {
				item.removeEventListener('click', handleDisableSorting);
				item.addEventListener('click', reloadTable);
			}
		};
	}
	
	if ( document.querySelectorAll('.dt-button').length ) {
		const buttons = document.querySelectorAll('.dt-button');
		for( let btn of  buttons) {
			btn.classList.add( 'hidden');
		}
	}


	settings?.table_export.forEach((btn) => {
		if (document.querySelector("." + btn + "_btn")) {
			document.querySelector("." + btn + "_btn").classList.remove('hidden');
		}
	});

	if ( document.querySelectorAll('.swptls-table-link').length ) {
		const links = document.querySelectorAll('.swptls-table-link');

		for( let link of links ) {
			link.target = settings?.redirection_type || '_self';
		}
	}

	changeCellFormat(settings?.cell_format, '#create_tables_wrapper th, #create_tables_wrapper td');
	changeCellFormat(settings?.responsive_style, '#create_tables_wrapper th, #create_tables_wrapper td');
}