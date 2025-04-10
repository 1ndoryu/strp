% Parámetros del péndulo invertido
m = 0.5;  % Masa del péndulo (kg)
M = 5.0;  % Masa del carro (kg)
L = 0.5;  % Longitud del péndulo (m)
g = 9.81; % Aceleración de la gravedad (m/s^2)

% Matrices del sistema (modelo linealizado)
A = [0 1 0 0;
(M+m)*g/(M*L) 0 0 0;
0 0 0 1;
-m*g/M 0 0 0];
B = [0; 1/M; 0; -1/(M*L)];
C = [1 0 0 0;
0 0 1 0];
D = [0; 0];

% Crear el sistema de espacio de estados
sys = ss(A, B, C, D);

% Etiquetar las salidas
sys.OutputName = {'theta', 'x'};

% Simulación de la respuesta impulsiva
t = 0:0.01:10; % Tiempo de simulación
impulse(sys, t);
title('Respuesta impulsiva del péndulo invertido');
xlabel('Tiempo');
ylabel('Respuesta');
grid on;
