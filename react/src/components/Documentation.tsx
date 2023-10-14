import React, { useEffect } from 'react';
import Container from '../core/Container';
import Row from '../core/Row';
import Column from '../core/Column';

// import { book, videoPlay, support, AdsProducts, promoThumbnail } from './../icons';

// import { isProActive, getNonce } from '../Helpers';

function Documentation() {
	

	return (
		<Container>
			<Row customClass='documentation-flex-row'>
				<Column lg="3" sm="4">
					<div className="test">Doc</div>
				</Column>
			</Row>
		</Container>
	);
}

export default Documentation;
