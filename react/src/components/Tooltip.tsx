import React from 'react'
import { Tooltip as ReactTooltip } from 'react-tooltip';


import { infoIconWithQuestionMark } from '../icons';

const Tooltip = ({ content }) => {
	const randomId = Math.floor(Math.random() * (1 - 100 + 1)) + 1;

	return (
		<><ReactTooltip
			anchorId={`app-help-${randomId}`}
			content={content}					
		/>
			<div className='swptls-tooltip' id={`app-help-${randomId}`}>
				{infoIconWithQuestionMark}
			</div></>
	)
}

export default Tooltip