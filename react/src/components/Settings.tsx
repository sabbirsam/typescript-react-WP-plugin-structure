import React, { useState, useEffect } from 'react';
import { Route } from 'react-router-dom';
import { toast } from 'react-toastify';
import { infoIcon } from '../icons';
import Container from '../core/Container';
import Row from '../core/Row';
import Column from '../core/Column';

// import { getNonce, isProActive, isProInstalled } from './../Helpers';

// import CodeEditor from '@uiw/react-textarea-code-editor';

function Settings() {
	
	return (
		<Container>
			<Row>
				<Column sm="12">
					<div className="swptls-settings-wrap">Settings</div>
					
				</Column>
			</Row>
		</Container>
	);
}

export default Settings;
