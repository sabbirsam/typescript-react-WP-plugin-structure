import React, { useEffect } from 'react';
import { Routes, Route, useNavigate } from 'react-router-dom';
import { ToastContainer } from 'react-toastify';
import 'react-toastify/dist/ReactToastify.css';
import Column from '../core/Column';
import Container from '../core/Container';
import Row from '../core/Row';
import Dashboard from './Dashboard';
import Settings from './Settings';
import Documentation from './Documentation';

// Default Styles
import '../styles/main.scss';

function App() {
	return (
		<>
			<Container>
				<Row>
					<Column xs="12">

						<Routes>
							<Route 
								path="/" 
								element={<Dashboard />} 
							/>

							<Route 
								path="/settings" 
								element={<Settings />} />
							<Route 
								path="/doc" 
								element={<Documentation />} 
							/>
						</Routes>

						<ToastContainer
							position="top-right"
							autoClose={2000}
							hideProgressBar={true}
							newestOnTop={false}
							closeOnClick={false}
							rtl={false}
							pauseOnFocusLoss={true}
							pauseOnHover={true}
							theme="colored"
						/>
					</Column>
				</Row>
			</Container>
		</>
	);
}

export default App;
