<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>me.site</title>
  <meta property="og:title" content=me.site />
  <meta property="og:type" content="website" />
  <meta property="og:url" content="https://me.lukas-bownik.net/" />
  <meta property="og:description" content="Arkasian's Portfolio" />
  <meta property="og:image" content="https://me.lukas-bownik.net/img/claude_e_shannon.gif" />
  <meta name="theme-color" content="#607FBA">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="manifest" href="site.webmanifest">
  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/screen.css">
  <link rel="stylesheet" href="css/material-icons.css">
  <link type="text/plain" rel="author" href="humans.txt" />
  <link rel="stylesheet" href="https://unpkg.com/terminal.css@0.7.2/dist/terminal.min.css" />
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <script src="js/plugins.js" defer></script>
  <script src="js/site.js" defer></script>
</head>

<body id="app" class="fullscreen">
  <main style="background-color: #607FBA; height: 100vh; ">
    <div class="screen" tabindex="0">
      <div class="screen-grid" ref="screengrid">
        {{ message }}
      </div>
    </div>
  </main>
</body>
<script>
  const { createApp } = Vue
  let commandBuffer = "";
  let sourceChosen = false;
  let source = null;
  const commands = [
    ".SYSSTA",
    ".USRNFO",
    "DIR",
    "RST",
    "?"
  ];
  const sources = [
    "S1"
  ];
  const baseUrl = "https://core.lukas-bownik.net/Api/v1/Command";
  createApp({
    data() {
      return {
        message: '!INIT ERR'
      }
    },
    mounted() {
      const $this = this;
      this.askToChooseSource();
      document.addEventListener('keydown', function (evt) {
        if(evt.key === "Enter"){
          $this.executeCommand(commandBuffer);
          commandBuffer = "";
          $this.$refs.screengrid.innerText += "\n";
        }

        if($this.validateKeyboardKey(evt.key) || $this.validateKeyboardCode(evt.keyCode)){
          $this.$refs.screengrid.innerText += evt.key;
          commandBuffer += evt.key;
          $this.$refs.screengrid.scrollTo(0, $this.$refs.screengrid.scrollTop + 1);
        }
      });
    },
    methods: {
      executeCommand(command) {
        command = command.toUpperCase();
        if(!sourceChosen && sources.includes(command)){
          this.setSource(command);
          return;
        }
        if(sourceChosen && commands.includes(command)){
          // If the command starts with . it is a remote command, otherwise local
          if(command.startsWith(".")){
            this.executeApiCall(command);
          } else {
            if(command === "?") {
              this.printCommands();
            }
            if(command === "RST"){
              this.reset();
            }
          }
        } else if(sourceChosen) {
          this.refreshScreen("!ERROR");
        } else {
          this.askToChooseSource();
        }
      },
      validateKeyboardKey(input) {
        return input == '.' || input == "?";
      },
      validateKeyboardCode(code) {
         // Small ASCII letters and digits
          return code >= 65 && code <= 93 || code >= 48 && code <= 57;
      },
      handleBackspaceKey() {;
        commandBuffer = commandBuffer.substring(0, commandBuffer.length - 1);
        this.$refs.screengrid.innerText = commandBuffer;
      },
      refreshScreen(output) {
        this.$refs.screengrid.innerText = output;
      },
      executeApiCall(command) {
        fetch(`${baseUrl}/${command.toUpperCase()}`,
        {
          method: 'POST',
          headers: {
            "Origin": "localhost:5000"
          }
        })
        .then(r => {
            console.log(r);
        })
        .catch(e => {
            this.refreshScreen(`!ERROR - ${source} RETURNED UNEXPECTED RESPONSE\n`);
        });
      },
      setSource(command) {
          sourceChosen = true;
          source = command;
          this.refreshScreen("MAX SRC# - 1, CLOUD CONSOLE #1");
      },
      askToChooseSource() {
        this.refreshScreen("S=");
      },
      printCommands() {
        let commandsText = "";
        for(let cmd in commands){
          commandsText += commands[cmd] + "\n";
        }
        this.$refs.screengrid.innerText = commandsText;
      },
      reset(){
        source = null;
        sourceChosen = false;
        this.askToChooseSource();
      }
    }
  }).mount('#app')
</script>

</html>

