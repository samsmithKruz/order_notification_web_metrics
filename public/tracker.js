(function () {
    function Metrics() {
      // Initialize some default values, like session start time and user info
      this.startTime = Date.now();
      this.userData = this.getUserData();
      this.sessionId = this.generateSessionId();
      this.pageViewData = []; // Store page view data
    }
  
    // Generate session ID
    Metrics.prototype.generateSessionId = function () {
      return "session-" + Math.random().toString(36).substr(2, 16);
    };
  
    // Get basic user data
    Metrics.prototype.getUserData = function () {
      return {
        browser: this.getBrowser(),
        device: this.getDeviceType(),
        os: this.getOS(),
        screenResolution: this.getScreenResolution(),
        referrer: document.referrer,
      };
    };
  
    // Get browser name
    Metrics.prototype.getBrowser = function () {
      const userAgent = navigator.userAgent;
      let browserName = "Unknown";
  
      if (userAgent.indexOf("Chrome") > -1) {
        browserName = "Chrome";
      } else if (userAgent.indexOf("Firefox") > -1) {
        browserName = "Firefox";
      } else if (userAgent.indexOf("Safari") > -1) {
        browserName = "Safari";
      } else if (userAgent.indexOf("Edge") > -1) {
        browserName = "Edge";
      }
  
      return (
        browserName +
        " " +
        (navigator.userAgent.match(/(Chrome|Firefox|Safari|Edge)\/([0-9\.]+)/) ||
          [])[2]
      );
    };
  
    // Get OS
    Metrics.prototype.getOS = function () {
      const userAgent = navigator.userAgent;
      if (userAgent.indexOf("Windows NT") > -1) {
        return "Windows";
      } else if (userAgent.indexOf("Mac OS X") > -1) {
        return "macOS";
      } else if (userAgent.indexOf("Android") > -1) {
        return "Android";
      } else if (userAgent.indexOf("iPhone") > -1) {
        return "iOS";
      }
      return "Unknown OS";
    };
  
    // Get Device type (Desktop, Tablet, Mobile)
    Metrics.prototype.getDeviceType = function () {
      const userAgent = navigator.userAgent;
      if (/mobile/i.test(userAgent)) {
        return "Mobile";
      } else if (/tablet/i.test(userAgent)) {
        return "Tablet";
      } else {
        return "Desktop";
      }
    };
  
    // Get screen resolution
    Metrics.prototype.getScreenResolution = function () {
      return window.innerWidth + "x" + window.innerHeight;
    };
  
    // Track Page View and send it
    Metrics.prototype.trackPageView = function () {
      const pageView = {
        type: "pageView",
        data: {
          page: window.location.pathname,
          referrer: this.userData.referrer,
          sessionId: this.sessionId, // Include sessionId
          timestamp: Date.now(),
        },
      };
  
      // Add to page view data array
      this.pageViewData.push(pageView);
  
      // Send metrics when threshold is met
      this.send();
    };
  
    // Send collected metrics
    Metrics.prototype.send = function () {
      if (this.pageViewData.length > 0) {
        fetch(this.getScriptUrl(), {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(this.pageViewData),
        })
          .then((response) => response.json())
          .then((result) => {
            console.log("Metrics sent successfully:", result);
          })
          .catch((error) => {
            console.error("Error sending metrics:", error);
          });
  
        // Clear page view data after sending
        this.pageViewData = [];
      }
    };
  
    // Get the correct API endpoint by extracting base URL from the script src
    Metrics.prototype.getScriptUrl = function () {
      const script = document.getElementById("order_metrics_tracker");
      if (script && script.src) {
        const url = new URL(script.src);
        return `${url.protocol}//${url.host}/api/metrics`; // Return the base URL with the API endpoint
      }
      return ""; // Return empty string if script is not found or src is invalid
    };
  
    // Initialize and handle beforeunload event
    Metrics.prototype.initialize = function () {
      // Track page view on load
      this.trackPageView();
  
      // Handle beforeunload event (send metrics when user is leaving the page)
      window.addEventListener("beforeunload", () => {
        this.send(); // Send any pending metrics before the user leaves
      });
    };
  
    // Instantiate and initialize the Metrics class
    window.metrics = new Metrics();
    metrics.initialize();
  })();
  