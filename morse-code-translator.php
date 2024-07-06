<?php
/*
Plugin Name: Morse Code Translator
Description: A simple Morse Code Translator with audio playback and WPM control.
Version: 1.0
Author: Vaishabh Jalmi
*/

function morse_code_translator_shortcode() {
    ob_start();
    ?>
    <div id="morse-translator">
        <h2>Morse Code Translator</h2>
        <textarea id="text-input" placeholder="Enter text here..."></textarea>
        <button onclick="translateToMorse()">Translate to Morse Code</button>
        <textarea id="morse-output" readonly placeholder="Morse code will appear here..."></textarea>

        <textarea id="morse-input" placeholder="Enter Morse code here (use . and -)"></textarea>
        <button onclick="translateToText()">Translate to Text</button>
        <textarea id="text-output" readonly placeholder="Text will appear here..."></textarea>

        <div class="control-group">
            <label for="wpm">Speed (WPM):</label>
            <input type="number" id="wpm" value="20" min="1">
        </div>
        <button onclick="playMorseCode()">Play Morse Code</button>
        <button onclick="stopMorseCode()">Stop</button>
    </div>

    <style>
        #morse-translator {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
            margin: 20px auto;
            text-align: center;
            box-sizing: border-box;
        }
        h2 {
            margin-top: 0;
        }
        textarea {
            width: 100%;
            height: 80px;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
            background: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px 0;
            width: 100%;
            box-sizing: border-box;
        }
        button:hover {
            background: #0056b3;
        }
        .control-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .control-group label {
            flex: 1;
            text-align: left;
        }
        .control-group input {
            flex: 2;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>

    <script>
        const morseCodeMap = {
            'A': '.-', 'B': '-...', 'C': '-.-.', 'D': '-..', 'E': '.', 'F': '..-.',
            'G': '--.', 'H': '....', 'I': '..', 'J': '.---', 'K': '-.-', 'L': '.-..',
            'M': '--', 'N': '-.', 'O': '---', 'P': '.--.', 'Q': '--.-', 'R': '.-.',
            'S': '...', 'T': '-', 'U': '..-', 'V': '...-', 'W': '.--', 'X': '-..-',
            'Y': '-.--', 'Z': '--..',
            '0': '-----', '1': '.----', '2': '..---', '3': '...--', '4': '....-',
            '5': '.....', '6': '-....', '7': '--...', '8': '---..', '9': '----.',
            ' ': '/'
        };

        function translateToMorse() {
            const text = document.getElementById('text-input').value.toUpperCase();
            const morseCode = text.split('').map(char => morseCodeMap[char] || '').join(' ');
            document.getElementById('morse-output').value = morseCode;
        }

        function translateToText() {
            const morseCode = document.getElementById('morse-input').value.trim();
            const text = morseCode.split(' ').map(code => Object.keys(morseCodeMap).find(key => morseCodeMap[key] === code) || '').join('');
            document.getElementById('text-output').value = text;
        }

        let audioContext = new (window.AudioContext || window.webkitAudioContext)();
        let oscillator;
        let gainNode;
        let dotDuration = 0.1; // seconds

        function playMorseCode() {
            const morseCode = document.getElementById('morse-output').value;
            const wpm = parseInt(document.getElementById('wpm').value) || 20;

            // Calculate dot duration based on WPM (Words Per Minute)
            dotDuration = 1.2 / wpm;

            if (!morseCode) return;

            let time = audioContext.currentTime;
            gainNode = audioContext.createGain();
            gainNode.connect(audioContext.destination);

            oscillator = audioContext.createOscillator();
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(600, time);
            oscillator.connect(gainNode);

            oscillator.start(time);

            for (let char of morseCode) {
                if (char === '.') {
                    gainNode.gain.setValueAtTime(1, time);
                    time += dotDuration;
                    gainNode.gain.setValueAtTime(0, time);
                    time += dotDuration;
                } else if (char === '-') {
                    gainNode.gain.setValueAtTime(1, time);
                    time += 3 * dotDuration;
                    gainNode.gain.setValueAtTime(0, time);
                    time += dotDuration;
                } else if (char === ' ') {
                    time += 3 * dotDuration;
                }
            }

            oscillator.stop(time);
        }

        function stopMorseCode() {
            if (oscillator) {
                oscillator.stop();
            }
        }
    </script>
    <?php
    return ob_get_clean();
}

add_shortcode('morse_translator', 'morse_code_translator_shortcode');
?>
