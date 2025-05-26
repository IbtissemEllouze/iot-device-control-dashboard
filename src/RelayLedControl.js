import React, { useState, useEffect } from 'react';
import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  Tooltip,
  CartesianGrid,
  ResponsiveContainer,
} from 'recharts';

function RelayLedControl() {
  const [donnees, setDonnees] = useState([]);
  const [ledState, setLedState] = useState(null);
  const [relayState, setRelayState] = useState(null);

  // Charger les données capteurs depuis l'API
  useEffect(() => {
    fetch('http://localhost/my-app/api/fetch_etat.php')
      .then((res) => res.json())
      .then((data) => {
        // Adapter le format pour Recharts (tableau d'objets avec une clé "name")
        const converted = data.map((item, index) => ({
          name: `Point ${index + 1}`,
          gaz: Number(item.gaz),
          pression: Number(item.pression),
          deplacement: Number(item.deplacement),
        }));
        setDonnees(converted);
      })
      .catch((err) => {
        console.error('Erreur lors du chargement des données capteurs :', err);
      });
  }, []);

  // Charger l'état LED et Relai
  useEffect(() => {
    fetch('http://localhost/my-app/api/get_ibtissem_status.php')
      .then((res) => res.json())
      .then((data) => {
        setLedState(data.led);
        setRelayState(data.relay);
      })
      .catch((err) => {
        console.error('Erreur lors du chargement des états LED/Relai :', err);
      });
  }, []);

  // Mettre à jour l'état LED/Relai via API
  function updateState(newLed, newRelay) {
    fetch(`http://localhost/my-app/api/update_ibtissem_status.php?led=${newLed}&relay=${newRelay}`)
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          setLedState(newLed);
          setRelayState(newRelay);
          console.log('Mise à jour réussie:', data.message);
        } else {
          console.error('Erreur:', data.message);
        }
      })
      .catch((err) => {
        console.error('Erreur API:', err);
      });
  }

  return (
    <div className="camera-container">
      <h2>Graphique des données capteurs</h2>
      <div className="graph-container" style={{ width: '100%', height: 300 }}>
        {donnees.length > 0 ? (
          <ResponsiveContainer>
            <LineChart data={donnees}>
              <CartesianGrid strokeDasharray="3 3" />
              <XAxis dataKey="name" />
              <YAxis />
              <Tooltip />
              <Line type="monotone" dataKey="gaz" stroke="#ff4d4d" />
              <Line type="monotone" dataKey="pression" stroke="#9c27b0" />
              <Line type="monotone" dataKey="deplacement" stroke="#4db8ff" />
            </LineChart>
          </ResponsiveContainer>
        ) : (
          <p>Chargement des données...</p>
        )}
      </div>

      <div className="control-panel" style={{ marginTop: 20 }}>
        <h3>Contrôle des dispositifs</h3>
        <p>LED: {ledState === null ? 'Chargement...' : (ledState === '1' ? 'Allumé' : 'Éteint')}</p>
        <p>Relai: {relayState === null ? 'Chargement...' : (relayState === '1' ? 'Activé' : 'Désactivé')}</p>

        <button onClick={() => updateState('1', relayState)} className="button-led" style={{ marginRight: 10 }}>
          💡 Allumer LED
        </button>
        <button onClick={() => updateState('0', relayState)} className="button-led" style={{ marginRight: 10 }}>
          💤 Éteindre LED
        </button>

        <button onClick={() => updateState(ledState, '1')} className="button-relai" style={{ marginRight: 10 }}>
          🔌 Activer Relai
        </button>
        <button onClick={() => updateState(ledState, '0')} className="button-relai">
          ❌ Désactiver Relai
        </button>
      </div>
    </div>
  );
}

export default RelayLedControl;
