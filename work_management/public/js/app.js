/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/axios/index.js":
/*!*************************************!*\
  !*** ./node_modules/axios/index.js ***!
  \*************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

module.exports = __webpack_require__(/*! ./lib/axios */ "./node_modules/axios/lib/axios.js");

/***/ }),

/***/ "./node_modules/axios/lib/adapters/xhr.js":
/*!************************************************!*\
  !*** ./node_modules/axios/lib/adapters/xhr.js ***!
  \************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
var settle = __webpack_require__(/*! ./../core/settle */ "./node_modules/axios/lib/core/settle.js");
var cookies = __webpack_require__(/*! ./../helpers/cookies */ "./node_modules/axios/lib/helpers/cookies.js");
var buildURL = __webpack_require__(/*! ./../helpers/buildURL */ "./node_modules/axios/lib/helpers/buildURL.js");
var buildFullPath = __webpack_require__(/*! ../core/buildFullPath */ "./node_modules/axios/lib/core/buildFullPath.js");
var parseHeaders = __webpack_require__(/*! ./../helpers/parseHeaders */ "./node_modules/axios/lib/helpers/parseHeaders.js");
var isURLSameOrigin = __webpack_require__(/*! ./../helpers/isURLSameOrigin */ "./node_modules/axios/lib/helpers/isURLSameOrigin.js");
var createError = __webpack_require__(/*! ../core/createError */ "./node_modules/axios/lib/core/createError.js");

module.exports = function xhrAdapter(config) {
  return new Promise(function dispatchXhrRequest(resolve, reject) {
    var requestData = config.data;
    var requestHeaders = config.headers;

    if (utils.isFormData(requestData)) {
      delete requestHeaders['Content-Type']; // Let the browser set it
    }

    var request = new XMLHttpRequest();

    // HTTP basic authentication
    if (config.auth) {
      var username = config.auth.username || '';
      var password = config.auth.password ? unescape(encodeURIComponent(config.auth.password)) : '';
      requestHeaders.Authorization = 'Basic ' + btoa(username + ':' + password);
    }

    var fullPath = buildFullPath(config.baseURL, config.url);
    request.open(config.method.toUpperCase(), buildURL(fullPath, config.params, config.paramsSerializer), true);

    // Set the request timeout in MS
    request.timeout = config.timeout;

    // Listen for ready state
    request.onreadystatechange = function handleLoad() {
      if (!request || request.readyState !== 4) {
        return;
      }

      // The request errored out and we didn't get a response, this will be
      // handled by onerror instead
      // With one exception: request that using file: protocol, most browsers
      // will return status as 0 even though it's a successful request
      if (request.status === 0 && !(request.responseURL && request.responseURL.indexOf('file:') === 0)) {
        return;
      }

      // Prepare the response
      var responseHeaders = 'getAllResponseHeaders' in request ? parseHeaders(request.getAllResponseHeaders()) : null;
      var responseData = !config.responseType || config.responseType === 'text' ? request.responseText : request.response;
      var response = {
        data: responseData,
        status: request.status,
        statusText: request.statusText,
        headers: responseHeaders,
        config: config,
        request: request
      };

      settle(resolve, reject, response);

      // Clean up request
      request = null;
    };

    // Handle browser request cancellation (as opposed to a manual cancellation)
    request.onabort = function handleAbort() {
      if (!request) {
        return;
      }

      reject(createError('Request aborted', config, 'ECONNABORTED', request));

      // Clean up request
      request = null;
    };

    // Handle low level network errors
    request.onerror = function handleError() {
      // Real errors are hidden from us by the browser
      // onerror should only fire if it's a network error
      reject(createError('Network Error', config, null, request));

      // Clean up request
      request = null;
    };

    // Handle timeout
    request.ontimeout = function handleTimeout() {
      var timeoutErrorMessage = 'timeout of ' + config.timeout + 'ms exceeded';
      if (config.timeoutErrorMessage) {
        timeoutErrorMessage = config.timeoutErrorMessage;
      }
      reject(createError(timeoutErrorMessage, config, 'ECONNABORTED',
        request));

      // Clean up request
      request = null;
    };

    // Add xsrf header
    // This is only done if running in a standard browser environment.
    // Specifically not if we're in a web worker, or react-native.
    if (utils.isStandardBrowserEnv()) {
      // Add xsrf header
      var xsrfValue = (config.withCredentials || isURLSameOrigin(fullPath)) && config.xsrfCookieName ?
        cookies.read(config.xsrfCookieName) :
        undefined;

      if (xsrfValue) {
        requestHeaders[config.xsrfHeaderName] = xsrfValue;
      }
    }

    // Add headers to the request
    if ('setRequestHeader' in request) {
      utils.forEach(requestHeaders, function setRequestHeader(val, key) {
        if (typeof requestData === 'undefined' && key.toLowerCase() === 'content-type') {
          // Remove Content-Type if data is undefined
          delete requestHeaders[key];
        } else {
          // Otherwise add header to the request
          request.setRequestHeader(key, val);
        }
      });
    }

    // Add withCredentials to request if needed
    if (!utils.isUndefined(config.withCredentials)) {
      request.withCredentials = !!config.withCredentials;
    }

    // Add responseType to request if needed
    if (config.responseType) {
      try {
        request.responseType = config.responseType;
      } catch (e) {
        // Expected DOMException thrown by browsers not compatible XMLHttpRequest Level 2.
        // But, this can be suppressed for 'json' type as it can be parsed by default 'transformResponse' function.
        if (config.responseType !== 'json') {
          throw e;
        }
      }
    }

    // Handle progress if needed
    if (typeof config.onDownloadProgress === 'function') {
      request.addEventListener('progress', config.onDownloadProgress);
    }

    // Not all browsers support upload events
    if (typeof config.onUploadProgress === 'function' && request.upload) {
      request.upload.addEventListener('progress', config.onUploadProgress);
    }

    if (config.cancelToken) {
      // Handle cancellation
      config.cancelToken.promise.then(function onCanceled(cancel) {
        if (!request) {
          return;
        }

        request.abort();
        reject(cancel);
        // Clean up request
        request = null;
      });
    }

    if (!requestData) {
      requestData = null;
    }

    // Send the request
    request.send(requestData);
  });
};


/***/ }),

/***/ "./node_modules/axios/lib/axios.js":
/*!*****************************************!*\
  !*** ./node_modules/axios/lib/axios.js ***!
  \*****************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./utils */ "./node_modules/axios/lib/utils.js");
var bind = __webpack_require__(/*! ./helpers/bind */ "./node_modules/axios/lib/helpers/bind.js");
var Axios = __webpack_require__(/*! ./core/Axios */ "./node_modules/axios/lib/core/Axios.js");
var mergeConfig = __webpack_require__(/*! ./core/mergeConfig */ "./node_modules/axios/lib/core/mergeConfig.js");
var defaults = __webpack_require__(/*! ./defaults */ "./node_modules/axios/lib/defaults.js");

/**
 * Create an instance of Axios
 *
 * @param {Object} defaultConfig The default config for the instance
 * @return {Axios} A new instance of Axios
 */
function createInstance(defaultConfig) {
  var context = new Axios(defaultConfig);
  var instance = bind(Axios.prototype.request, context);

  // Copy axios.prototype to instance
  utils.extend(instance, Axios.prototype, context);

  // Copy context to instance
  utils.extend(instance, context);

  return instance;
}

// Create the default instance to be exported
var axios = createInstance(defaults);

// Expose Axios class to allow class inheritance
axios.Axios = Axios;

// Factory for creating new instances
axios.create = function create(instanceConfig) {
  return createInstance(mergeConfig(axios.defaults, instanceConfig));
};

// Expose Cancel & CancelToken
axios.Cancel = __webpack_require__(/*! ./cancel/Cancel */ "./node_modules/axios/lib/cancel/Cancel.js");
axios.CancelToken = __webpack_require__(/*! ./cancel/CancelToken */ "./node_modules/axios/lib/cancel/CancelToken.js");
axios.isCancel = __webpack_require__(/*! ./cancel/isCancel */ "./node_modules/axios/lib/cancel/isCancel.js");

// Expose all/spread
axios.all = function all(promises) {
  return Promise.all(promises);
};
axios.spread = __webpack_require__(/*! ./helpers/spread */ "./node_modules/axios/lib/helpers/spread.js");

// Expose isAxiosError
axios.isAxiosError = __webpack_require__(/*! ./helpers/isAxiosError */ "./node_modules/axios/lib/helpers/isAxiosError.js");

module.exports = axios;

// Allow use of default import syntax in TypeScript
module.exports.default = axios;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/Cancel.js":
/*!*************************************************!*\
  !*** ./node_modules/axios/lib/cancel/Cancel.js ***!
  \*************************************************/
/***/ ((module) => {

"use strict";


/**
 * A `Cancel` is an object that is thrown when an operation is canceled.
 *
 * @class
 * @param {string=} message The message.
 */
function Cancel(message) {
  this.message = message;
}

Cancel.prototype.toString = function toString() {
  return 'Cancel' + (this.message ? ': ' + this.message : '');
};

Cancel.prototype.__CANCEL__ = true;

module.exports = Cancel;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/CancelToken.js":
/*!******************************************************!*\
  !*** ./node_modules/axios/lib/cancel/CancelToken.js ***!
  \******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var Cancel = __webpack_require__(/*! ./Cancel */ "./node_modules/axios/lib/cancel/Cancel.js");

/**
 * A `CancelToken` is an object that can be used to request cancellation of an operation.
 *
 * @class
 * @param {Function} executor The executor function.
 */
function CancelToken(executor) {
  if (typeof executor !== 'function') {
    throw new TypeError('executor must be a function.');
  }

  var resolvePromise;
  this.promise = new Promise(function promiseExecutor(resolve) {
    resolvePromise = resolve;
  });

  var token = this;
  executor(function cancel(message) {
    if (token.reason) {
      // Cancellation has already been requested
      return;
    }

    token.reason = new Cancel(message);
    resolvePromise(token.reason);
  });
}

/**
 * Throws a `Cancel` if cancellation has been requested.
 */
CancelToken.prototype.throwIfRequested = function throwIfRequested() {
  if (this.reason) {
    throw this.reason;
  }
};

/**
 * Returns an object that contains a new `CancelToken` and a function that, when called,
 * cancels the `CancelToken`.
 */
CancelToken.source = function source() {
  var cancel;
  var token = new CancelToken(function executor(c) {
    cancel = c;
  });
  return {
    token: token,
    cancel: cancel
  };
};

module.exports = CancelToken;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/isCancel.js":
/*!***************************************************!*\
  !*** ./node_modules/axios/lib/cancel/isCancel.js ***!
  \***************************************************/
/***/ ((module) => {

"use strict";


module.exports = function isCancel(value) {
  return !!(value && value.__CANCEL__);
};


/***/ }),

/***/ "./node_modules/axios/lib/core/Axios.js":
/*!**********************************************!*\
  !*** ./node_modules/axios/lib/core/Axios.js ***!
  \**********************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
var buildURL = __webpack_require__(/*! ../helpers/buildURL */ "./node_modules/axios/lib/helpers/buildURL.js");
var InterceptorManager = __webpack_require__(/*! ./InterceptorManager */ "./node_modules/axios/lib/core/InterceptorManager.js");
var dispatchRequest = __webpack_require__(/*! ./dispatchRequest */ "./node_modules/axios/lib/core/dispatchRequest.js");
var mergeConfig = __webpack_require__(/*! ./mergeConfig */ "./node_modules/axios/lib/core/mergeConfig.js");

/**
 * Create a new instance of Axios
 *
 * @param {Object} instanceConfig The default config for the instance
 */
function Axios(instanceConfig) {
  this.defaults = instanceConfig;
  this.interceptors = {
    request: new InterceptorManager(),
    response: new InterceptorManager()
  };
}

/**
 * Dispatch a request
 *
 * @param {Object} config The config specific for this request (merged with this.defaults)
 */
Axios.prototype.request = function request(config) {
  /*eslint no-param-reassign:0*/
  // Allow for axios('example/url'[, config]) a la fetch API
  if (typeof config === 'string') {
    config = arguments[1] || {};
    config.url = arguments[0];
  } else {
    config = config || {};
  }

  config = mergeConfig(this.defaults, config);

  // Set config.method
  if (config.method) {
    config.method = config.method.toLowerCase();
  } else if (this.defaults.method) {
    config.method = this.defaults.method.toLowerCase();
  } else {
    config.method = 'get';
  }

  // Hook up interceptors middleware
  var chain = [dispatchRequest, undefined];
  var promise = Promise.resolve(config);

  this.interceptors.request.forEach(function unshiftRequestInterceptors(interceptor) {
    chain.unshift(interceptor.fulfilled, interceptor.rejected);
  });

  this.interceptors.response.forEach(function pushResponseInterceptors(interceptor) {
    chain.push(interceptor.fulfilled, interceptor.rejected);
  });

  while (chain.length) {
    promise = promise.then(chain.shift(), chain.shift());
  }

  return promise;
};

Axios.prototype.getUri = function getUri(config) {
  config = mergeConfig(this.defaults, config);
  return buildURL(config.url, config.params, config.paramsSerializer).replace(/^\?/, '');
};

// Provide aliases for supported request methods
utils.forEach(['delete', 'get', 'head', 'options'], function forEachMethodNoData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, config) {
    return this.request(mergeConfig(config || {}, {
      method: method,
      url: url,
      data: (config || {}).data
    }));
  };
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, data, config) {
    return this.request(mergeConfig(config || {}, {
      method: method,
      url: url,
      data: data
    }));
  };
});

module.exports = Axios;


/***/ }),

/***/ "./node_modules/axios/lib/core/InterceptorManager.js":
/*!***********************************************************!*\
  !*** ./node_modules/axios/lib/core/InterceptorManager.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

function InterceptorManager() {
  this.handlers = [];
}

/**
 * Add a new interceptor to the stack
 *
 * @param {Function} fulfilled The function to handle `then` for a `Promise`
 * @param {Function} rejected The function to handle `reject` for a `Promise`
 *
 * @return {Number} An ID used to remove interceptor later
 */
InterceptorManager.prototype.use = function use(fulfilled, rejected) {
  this.handlers.push({
    fulfilled: fulfilled,
    rejected: rejected
  });
  return this.handlers.length - 1;
};

/**
 * Remove an interceptor from the stack
 *
 * @param {Number} id The ID that was returned by `use`
 */
InterceptorManager.prototype.eject = function eject(id) {
  if (this.handlers[id]) {
    this.handlers[id] = null;
  }
};

/**
 * Iterate over all the registered interceptors
 *
 * This method is particularly useful for skipping over any
 * interceptors that may have become `null` calling `eject`.
 *
 * @param {Function} fn The function to call for each interceptor
 */
InterceptorManager.prototype.forEach = function forEach(fn) {
  utils.forEach(this.handlers, function forEachHandler(h) {
    if (h !== null) {
      fn(h);
    }
  });
};

module.exports = InterceptorManager;


/***/ }),

/***/ "./node_modules/axios/lib/core/buildFullPath.js":
/*!******************************************************!*\
  !*** ./node_modules/axios/lib/core/buildFullPath.js ***!
  \******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var isAbsoluteURL = __webpack_require__(/*! ../helpers/isAbsoluteURL */ "./node_modules/axios/lib/helpers/isAbsoluteURL.js");
var combineURLs = __webpack_require__(/*! ../helpers/combineURLs */ "./node_modules/axios/lib/helpers/combineURLs.js");

/**
 * Creates a new URL by combining the baseURL with the requestedURL,
 * only when the requestedURL is not already an absolute URL.
 * If the requestURL is absolute, this function returns the requestedURL untouched.
 *
 * @param {string} baseURL The base URL
 * @param {string} requestedURL Absolute or relative URL to combine
 * @returns {string} The combined full path
 */
module.exports = function buildFullPath(baseURL, requestedURL) {
  if (baseURL && !isAbsoluteURL(requestedURL)) {
    return combineURLs(baseURL, requestedURL);
  }
  return requestedURL;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/createError.js":
/*!****************************************************!*\
  !*** ./node_modules/axios/lib/core/createError.js ***!
  \****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var enhanceError = __webpack_require__(/*! ./enhanceError */ "./node_modules/axios/lib/core/enhanceError.js");

/**
 * Create an Error with the specified message, config, error code, request and response.
 *
 * @param {string} message The error message.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The created error.
 */
module.exports = function createError(message, config, code, request, response) {
  var error = new Error(message);
  return enhanceError(error, config, code, request, response);
};


/***/ }),

/***/ "./node_modules/axios/lib/core/dispatchRequest.js":
/*!********************************************************!*\
  !*** ./node_modules/axios/lib/core/dispatchRequest.js ***!
  \********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
var transformData = __webpack_require__(/*! ./transformData */ "./node_modules/axios/lib/core/transformData.js");
var isCancel = __webpack_require__(/*! ../cancel/isCancel */ "./node_modules/axios/lib/cancel/isCancel.js");
var defaults = __webpack_require__(/*! ../defaults */ "./node_modules/axios/lib/defaults.js");

/**
 * Throws a `Cancel` if cancellation has been requested.
 */
function throwIfCancellationRequested(config) {
  if (config.cancelToken) {
    config.cancelToken.throwIfRequested();
  }
}

/**
 * Dispatch a request to the server using the configured adapter.
 *
 * @param {object} config The config that is to be used for the request
 * @returns {Promise} The Promise to be fulfilled
 */
module.exports = function dispatchRequest(config) {
  throwIfCancellationRequested(config);

  // Ensure headers exist
  config.headers = config.headers || {};

  // Transform request data
  config.data = transformData(
    config.data,
    config.headers,
    config.transformRequest
  );

  // Flatten headers
  config.headers = utils.merge(
    config.headers.common || {},
    config.headers[config.method] || {},
    config.headers
  );

  utils.forEach(
    ['delete', 'get', 'head', 'post', 'put', 'patch', 'common'],
    function cleanHeaderConfig(method) {
      delete config.headers[method];
    }
  );

  var adapter = config.adapter || defaults.adapter;

  return adapter(config).then(function onAdapterResolution(response) {
    throwIfCancellationRequested(config);

    // Transform response data
    response.data = transformData(
      response.data,
      response.headers,
      config.transformResponse
    );

    return response;
  }, function onAdapterRejection(reason) {
    if (!isCancel(reason)) {
      throwIfCancellationRequested(config);

      // Transform response data
      if (reason && reason.response) {
        reason.response.data = transformData(
          reason.response.data,
          reason.response.headers,
          config.transformResponse
        );
      }
    }

    return Promise.reject(reason);
  });
};


/***/ }),

/***/ "./node_modules/axios/lib/core/enhanceError.js":
/*!*****************************************************!*\
  !*** ./node_modules/axios/lib/core/enhanceError.js ***!
  \*****************************************************/
/***/ ((module) => {

"use strict";


/**
 * Update an Error with the specified config, error code, and response.
 *
 * @param {Error} error The error to update.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The error.
 */
module.exports = function enhanceError(error, config, code, request, response) {
  error.config = config;
  if (code) {
    error.code = code;
  }

  error.request = request;
  error.response = response;
  error.isAxiosError = true;

  error.toJSON = function toJSON() {
    return {
      // Standard
      message: this.message,
      name: this.name,
      // Microsoft
      description: this.description,
      number: this.number,
      // Mozilla
      fileName: this.fileName,
      lineNumber: this.lineNumber,
      columnNumber: this.columnNumber,
      stack: this.stack,
      // Axios
      config: this.config,
      code: this.code
    };
  };
  return error;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/mergeConfig.js":
/*!****************************************************!*\
  !*** ./node_modules/axios/lib/core/mergeConfig.js ***!
  \****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ../utils */ "./node_modules/axios/lib/utils.js");

/**
 * Config-specific merge-function which creates a new config-object
 * by merging two configuration objects together.
 *
 * @param {Object} config1
 * @param {Object} config2
 * @returns {Object} New object resulting from merging config2 to config1
 */
module.exports = function mergeConfig(config1, config2) {
  // eslint-disable-next-line no-param-reassign
  config2 = config2 || {};
  var config = {};

  var valueFromConfig2Keys = ['url', 'method', 'data'];
  var mergeDeepPropertiesKeys = ['headers', 'auth', 'proxy', 'params'];
  var defaultToConfig2Keys = [
    'baseURL', 'transformRequest', 'transformResponse', 'paramsSerializer',
    'timeout', 'timeoutMessage', 'withCredentials', 'adapter', 'responseType', 'xsrfCookieName',
    'xsrfHeaderName', 'onUploadProgress', 'onDownloadProgress', 'decompress',
    'maxContentLength', 'maxBodyLength', 'maxRedirects', 'transport', 'httpAgent',
    'httpsAgent', 'cancelToken', 'socketPath', 'responseEncoding'
  ];
  var directMergeKeys = ['validateStatus'];

  function getMergedValue(target, source) {
    if (utils.isPlainObject(target) && utils.isPlainObject(source)) {
      return utils.merge(target, source);
    } else if (utils.isPlainObject(source)) {
      return utils.merge({}, source);
    } else if (utils.isArray(source)) {
      return source.slice();
    }
    return source;
  }

  function mergeDeepProperties(prop) {
    if (!utils.isUndefined(config2[prop])) {
      config[prop] = getMergedValue(config1[prop], config2[prop]);
    } else if (!utils.isUndefined(config1[prop])) {
      config[prop] = getMergedValue(undefined, config1[prop]);
    }
  }

  utils.forEach(valueFromConfig2Keys, function valueFromConfig2(prop) {
    if (!utils.isUndefined(config2[prop])) {
      config[prop] = getMergedValue(undefined, config2[prop]);
    }
  });

  utils.forEach(mergeDeepPropertiesKeys, mergeDeepProperties);

  utils.forEach(defaultToConfig2Keys, function defaultToConfig2(prop) {
    if (!utils.isUndefined(config2[prop])) {
      config[prop] = getMergedValue(undefined, config2[prop]);
    } else if (!utils.isUndefined(config1[prop])) {
      config[prop] = getMergedValue(undefined, config1[prop]);
    }
  });

  utils.forEach(directMergeKeys, function merge(prop) {
    if (prop in config2) {
      config[prop] = getMergedValue(config1[prop], config2[prop]);
    } else if (prop in config1) {
      config[prop] = getMergedValue(undefined, config1[prop]);
    }
  });

  var axiosKeys = valueFromConfig2Keys
    .concat(mergeDeepPropertiesKeys)
    .concat(defaultToConfig2Keys)
    .concat(directMergeKeys);

  var otherKeys = Object
    .keys(config1)
    .concat(Object.keys(config2))
    .filter(function filterAxiosKeys(key) {
      return axiosKeys.indexOf(key) === -1;
    });

  utils.forEach(otherKeys, mergeDeepProperties);

  return config;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/settle.js":
/*!***********************************************!*\
  !*** ./node_modules/axios/lib/core/settle.js ***!
  \***********************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var createError = __webpack_require__(/*! ./createError */ "./node_modules/axios/lib/core/createError.js");

/**
 * Resolve or reject a Promise based on response status.
 *
 * @param {Function} resolve A function that resolves the promise.
 * @param {Function} reject A function that rejects the promise.
 * @param {object} response The response.
 */
module.exports = function settle(resolve, reject, response) {
  var validateStatus = response.config.validateStatus;
  if (!response.status || !validateStatus || validateStatus(response.status)) {
    resolve(response);
  } else {
    reject(createError(
      'Request failed with status code ' + response.status,
      response.config,
      null,
      response.request,
      response
    ));
  }
};


/***/ }),

/***/ "./node_modules/axios/lib/core/transformData.js":
/*!******************************************************!*\
  !*** ./node_modules/axios/lib/core/transformData.js ***!
  \******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

/**
 * Transform the data for a request or a response
 *
 * @param {Object|String} data The data to be transformed
 * @param {Array} headers The headers for the request or response
 * @param {Array|Function} fns A single function or Array of functions
 * @returns {*} The resulting transformed data
 */
module.exports = function transformData(data, headers, fns) {
  /*eslint no-param-reassign:0*/
  utils.forEach(fns, function transform(fn) {
    data = fn(data, headers);
  });

  return data;
};


/***/ }),

/***/ "./node_modules/axios/lib/defaults.js":
/*!********************************************!*\
  !*** ./node_modules/axios/lib/defaults.js ***!
  \********************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var process = __webpack_require__(/*! process/browser */ "./node_modules/process/browser.js");


var utils = __webpack_require__(/*! ./utils */ "./node_modules/axios/lib/utils.js");
var normalizeHeaderName = __webpack_require__(/*! ./helpers/normalizeHeaderName */ "./node_modules/axios/lib/helpers/normalizeHeaderName.js");

var DEFAULT_CONTENT_TYPE = {
  'Content-Type': 'application/x-www-form-urlencoded'
};

function setContentTypeIfUnset(headers, value) {
  if (!utils.isUndefined(headers) && utils.isUndefined(headers['Content-Type'])) {
    headers['Content-Type'] = value;
  }
}

function getDefaultAdapter() {
  var adapter;
  if (typeof XMLHttpRequest !== 'undefined') {
    // For browsers use XHR adapter
    adapter = __webpack_require__(/*! ./adapters/xhr */ "./node_modules/axios/lib/adapters/xhr.js");
  } else if (typeof process !== 'undefined' && Object.prototype.toString.call(process) === '[object process]') {
    // For node use HTTP adapter
    adapter = __webpack_require__(/*! ./adapters/http */ "./node_modules/axios/lib/adapters/xhr.js");
  }
  return adapter;
}

var defaults = {
  adapter: getDefaultAdapter(),

  transformRequest: [function transformRequest(data, headers) {
    normalizeHeaderName(headers, 'Accept');
    normalizeHeaderName(headers, 'Content-Type');
    if (utils.isFormData(data) ||
      utils.isArrayBuffer(data) ||
      utils.isBuffer(data) ||
      utils.isStream(data) ||
      utils.isFile(data) ||
      utils.isBlob(data)
    ) {
      return data;
    }
    if (utils.isArrayBufferView(data)) {
      return data.buffer;
    }
    if (utils.isURLSearchParams(data)) {
      setContentTypeIfUnset(headers, 'application/x-www-form-urlencoded;charset=utf-8');
      return data.toString();
    }
    if (utils.isObject(data)) {
      setContentTypeIfUnset(headers, 'application/json;charset=utf-8');
      return JSON.stringify(data);
    }
    return data;
  }],

  transformResponse: [function transformResponse(data) {
    /*eslint no-param-reassign:0*/
    if (typeof data === 'string') {
      try {
        data = JSON.parse(data);
      } catch (e) { /* Ignore */ }
    }
    return data;
  }],

  /**
   * A timeout in milliseconds to abort a request. If set to 0 (default) a
   * timeout is not created.
   */
  timeout: 0,

  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',

  maxContentLength: -1,
  maxBodyLength: -1,

  validateStatus: function validateStatus(status) {
    return status >= 200 && status < 300;
  }
};

defaults.headers = {
  common: {
    'Accept': 'application/json, text/plain, */*'
  }
};

utils.forEach(['delete', 'get', 'head'], function forEachMethodNoData(method) {
  defaults.headers[method] = {};
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  defaults.headers[method] = utils.merge(DEFAULT_CONTENT_TYPE);
});

module.exports = defaults;


/***/ }),

/***/ "./node_modules/axios/lib/helpers/bind.js":
/*!************************************************!*\
  !*** ./node_modules/axios/lib/helpers/bind.js ***!
  \************************************************/
/***/ ((module) => {

"use strict";


module.exports = function bind(fn, thisArg) {
  return function wrap() {
    var args = new Array(arguments.length);
    for (var i = 0; i < args.length; i++) {
      args[i] = arguments[i];
    }
    return fn.apply(thisArg, args);
  };
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/buildURL.js":
/*!****************************************************!*\
  !*** ./node_modules/axios/lib/helpers/buildURL.js ***!
  \****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

function encode(val) {
  return encodeURIComponent(val).
    replace(/%3A/gi, ':').
    replace(/%24/g, '$').
    replace(/%2C/gi, ',').
    replace(/%20/g, '+').
    replace(/%5B/gi, '[').
    replace(/%5D/gi, ']');
}

/**
 * Build a URL by appending params to the end
 *
 * @param {string} url The base of the url (e.g., http://www.google.com)
 * @param {object} [params] The params to be appended
 * @returns {string} The formatted url
 */
module.exports = function buildURL(url, params, paramsSerializer) {
  /*eslint no-param-reassign:0*/
  if (!params) {
    return url;
  }

  var serializedParams;
  if (paramsSerializer) {
    serializedParams = paramsSerializer(params);
  } else if (utils.isURLSearchParams(params)) {
    serializedParams = params.toString();
  } else {
    var parts = [];

    utils.forEach(params, function serialize(val, key) {
      if (val === null || typeof val === 'undefined') {
        return;
      }

      if (utils.isArray(val)) {
        key = key + '[]';
      } else {
        val = [val];
      }

      utils.forEach(val, function parseValue(v) {
        if (utils.isDate(v)) {
          v = v.toISOString();
        } else if (utils.isObject(v)) {
          v = JSON.stringify(v);
        }
        parts.push(encode(key) + '=' + encode(v));
      });
    });

    serializedParams = parts.join('&');
  }

  if (serializedParams) {
    var hashmarkIndex = url.indexOf('#');
    if (hashmarkIndex !== -1) {
      url = url.slice(0, hashmarkIndex);
    }

    url += (url.indexOf('?') === -1 ? '?' : '&') + serializedParams;
  }

  return url;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/combineURLs.js":
/*!*******************************************************!*\
  !*** ./node_modules/axios/lib/helpers/combineURLs.js ***!
  \*******************************************************/
/***/ ((module) => {

"use strict";


/**
 * Creates a new URL by combining the specified URLs
 *
 * @param {string} baseURL The base URL
 * @param {string} relativeURL The relative URL
 * @returns {string} The combined URL
 */
module.exports = function combineURLs(baseURL, relativeURL) {
  return relativeURL
    ? baseURL.replace(/\/+$/, '') + '/' + relativeURL.replace(/^\/+/, '')
    : baseURL;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/cookies.js":
/*!***************************************************!*\
  !*** ./node_modules/axios/lib/helpers/cookies.js ***!
  \***************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

module.exports = (
  utils.isStandardBrowserEnv() ?

  // Standard browser envs support document.cookie
    (function standardBrowserEnv() {
      return {
        write: function write(name, value, expires, path, domain, secure) {
          var cookie = [];
          cookie.push(name + '=' + encodeURIComponent(value));

          if (utils.isNumber(expires)) {
            cookie.push('expires=' + new Date(expires).toGMTString());
          }

          if (utils.isString(path)) {
            cookie.push('path=' + path);
          }

          if (utils.isString(domain)) {
            cookie.push('domain=' + domain);
          }

          if (secure === true) {
            cookie.push('secure');
          }

          document.cookie = cookie.join('; ');
        },

        read: function read(name) {
          var match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
          return (match ? decodeURIComponent(match[3]) : null);
        },

        remove: function remove(name) {
          this.write(name, '', Date.now() - 86400000);
        }
      };
    })() :

  // Non standard browser env (web workers, react-native) lack needed support.
    (function nonStandardBrowserEnv() {
      return {
        write: function write() {},
        read: function read() { return null; },
        remove: function remove() {}
      };
    })()
);


/***/ }),

/***/ "./node_modules/axios/lib/helpers/isAbsoluteURL.js":
/*!*********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/isAbsoluteURL.js ***!
  \*********************************************************/
/***/ ((module) => {

"use strict";


/**
 * Determines whether the specified URL is absolute
 *
 * @param {string} url The URL to test
 * @returns {boolean} True if the specified URL is absolute, otherwise false
 */
module.exports = function isAbsoluteURL(url) {
  // A URL is considered absolute if it begins with "<scheme>://" or "//" (protocol-relative URL).
  // RFC 3986 defines scheme name as a sequence of characters beginning with a letter and followed
  // by any combination of letters, digits, plus, period, or hyphen.
  return /^([a-z][a-z\d\+\-\.]*:)?\/\//i.test(url);
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/isAxiosError.js":
/*!********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/isAxiosError.js ***!
  \********************************************************/
/***/ ((module) => {

"use strict";


/**
 * Determines whether the payload is an error thrown by Axios
 *
 * @param {*} payload The value to test
 * @returns {boolean} True if the payload is an error thrown by Axios, otherwise false
 */
module.exports = function isAxiosError(payload) {
  return (typeof payload === 'object') && (payload.isAxiosError === true);
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/isURLSameOrigin.js":
/*!***********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/isURLSameOrigin.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

module.exports = (
  utils.isStandardBrowserEnv() ?

  // Standard browser envs have full support of the APIs needed to test
  // whether the request URL is of the same origin as current location.
    (function standardBrowserEnv() {
      var msie = /(msie|trident)/i.test(navigator.userAgent);
      var urlParsingNode = document.createElement('a');
      var originURL;

      /**
    * Parse a URL to discover it's components
    *
    * @param {String} url The URL to be parsed
    * @returns {Object}
    */
      function resolveURL(url) {
        var href = url;

        if (msie) {
        // IE needs attribute set twice to normalize properties
          urlParsingNode.setAttribute('href', href);
          href = urlParsingNode.href;
        }

        urlParsingNode.setAttribute('href', href);

        // urlParsingNode provides the UrlUtils interface - http://url.spec.whatwg.org/#urlutils
        return {
          href: urlParsingNode.href,
          protocol: urlParsingNode.protocol ? urlParsingNode.protocol.replace(/:$/, '') : '',
          host: urlParsingNode.host,
          search: urlParsingNode.search ? urlParsingNode.search.replace(/^\?/, '') : '',
          hash: urlParsingNode.hash ? urlParsingNode.hash.replace(/^#/, '') : '',
          hostname: urlParsingNode.hostname,
          port: urlParsingNode.port,
          pathname: (urlParsingNode.pathname.charAt(0) === '/') ?
            urlParsingNode.pathname :
            '/' + urlParsingNode.pathname
        };
      }

      originURL = resolveURL(window.location.href);

      /**
    * Determine if a URL shares the same origin as the current location
    *
    * @param {String} requestURL The URL to test
    * @returns {boolean} True if URL shares the same origin, otherwise false
    */
      return function isURLSameOrigin(requestURL) {
        var parsed = (utils.isString(requestURL)) ? resolveURL(requestURL) : requestURL;
        return (parsed.protocol === originURL.protocol &&
            parsed.host === originURL.host);
      };
    })() :

  // Non standard browser envs (web workers, react-native) lack needed support.
    (function nonStandardBrowserEnv() {
      return function isURLSameOrigin() {
        return true;
      };
    })()
);


/***/ }),

/***/ "./node_modules/axios/lib/helpers/normalizeHeaderName.js":
/*!***************************************************************!*\
  !*** ./node_modules/axios/lib/helpers/normalizeHeaderName.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ../utils */ "./node_modules/axios/lib/utils.js");

module.exports = function normalizeHeaderName(headers, normalizedName) {
  utils.forEach(headers, function processHeader(value, name) {
    if (name !== normalizedName && name.toUpperCase() === normalizedName.toUpperCase()) {
      headers[normalizedName] = value;
      delete headers[name];
    }
  });
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/parseHeaders.js":
/*!********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/parseHeaders.js ***!
  \********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

// Headers whose duplicates are ignored by node
// c.f. https://nodejs.org/api/http.html#http_message_headers
var ignoreDuplicateOf = [
  'age', 'authorization', 'content-length', 'content-type', 'etag',
  'expires', 'from', 'host', 'if-modified-since', 'if-unmodified-since',
  'last-modified', 'location', 'max-forwards', 'proxy-authorization',
  'referer', 'retry-after', 'user-agent'
];

/**
 * Parse headers into an object
 *
 * ```
 * Date: Wed, 27 Aug 2014 08:58:49 GMT
 * Content-Type: application/json
 * Connection: keep-alive
 * Transfer-Encoding: chunked
 * ```
 *
 * @param {String} headers Headers needing to be parsed
 * @returns {Object} Headers parsed into an object
 */
module.exports = function parseHeaders(headers) {
  var parsed = {};
  var key;
  var val;
  var i;

  if (!headers) { return parsed; }

  utils.forEach(headers.split('\n'), function parser(line) {
    i = line.indexOf(':');
    key = utils.trim(line.substr(0, i)).toLowerCase();
    val = utils.trim(line.substr(i + 1));

    if (key) {
      if (parsed[key] && ignoreDuplicateOf.indexOf(key) >= 0) {
        return;
      }
      if (key === 'set-cookie') {
        parsed[key] = (parsed[key] ? parsed[key] : []).concat([val]);
      } else {
        parsed[key] = parsed[key] ? parsed[key] + ', ' + val : val;
      }
    }
  });

  return parsed;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/spread.js":
/*!**************************************************!*\
  !*** ./node_modules/axios/lib/helpers/spread.js ***!
  \**************************************************/
/***/ ((module) => {

"use strict";


/**
 * Syntactic sugar for invoking a function and expanding an array for arguments.
 *
 * Common use case would be to use `Function.prototype.apply`.
 *
 *  ```js
 *  function f(x, y, z) {}
 *  var args = [1, 2, 3];
 *  f.apply(null, args);
 *  ```
 *
 * With `spread` this example can be re-written.
 *
 *  ```js
 *  spread(function(x, y, z) {})([1, 2, 3]);
 *  ```
 *
 * @param {Function} callback
 * @returns {Function}
 */
module.exports = function spread(callback) {
  return function wrap(arr) {
    return callback.apply(null, arr);
  };
};


/***/ }),

/***/ "./node_modules/axios/lib/utils.js":
/*!*****************************************!*\
  !*** ./node_modules/axios/lib/utils.js ***!
  \*****************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var bind = __webpack_require__(/*! ./helpers/bind */ "./node_modules/axios/lib/helpers/bind.js");

/*global toString:true*/

// utils is a library of generic helper functions non-specific to axios

var toString = Object.prototype.toString;

/**
 * Determine if a value is an Array
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Array, otherwise false
 */
function isArray(val) {
  return toString.call(val) === '[object Array]';
}

/**
 * Determine if a value is undefined
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if the value is undefined, otherwise false
 */
function isUndefined(val) {
  return typeof val === 'undefined';
}

/**
 * Determine if a value is a Buffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Buffer, otherwise false
 */
function isBuffer(val) {
  return val !== null && !isUndefined(val) && val.constructor !== null && !isUndefined(val.constructor)
    && typeof val.constructor.isBuffer === 'function' && val.constructor.isBuffer(val);
}

/**
 * Determine if a value is an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an ArrayBuffer, otherwise false
 */
function isArrayBuffer(val) {
  return toString.call(val) === '[object ArrayBuffer]';
}

/**
 * Determine if a value is a FormData
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an FormData, otherwise false
 */
function isFormData(val) {
  return (typeof FormData !== 'undefined') && (val instanceof FormData);
}

/**
 * Determine if a value is a view on an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a view on an ArrayBuffer, otherwise false
 */
function isArrayBufferView(val) {
  var result;
  if ((typeof ArrayBuffer !== 'undefined') && (ArrayBuffer.isView)) {
    result = ArrayBuffer.isView(val);
  } else {
    result = (val) && (val.buffer) && (val.buffer instanceof ArrayBuffer);
  }
  return result;
}

/**
 * Determine if a value is a String
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a String, otherwise false
 */
function isString(val) {
  return typeof val === 'string';
}

/**
 * Determine if a value is a Number
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Number, otherwise false
 */
function isNumber(val) {
  return typeof val === 'number';
}

/**
 * Determine if a value is an Object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Object, otherwise false
 */
function isObject(val) {
  return val !== null && typeof val === 'object';
}

/**
 * Determine if a value is a plain Object
 *
 * @param {Object} val The value to test
 * @return {boolean} True if value is a plain Object, otherwise false
 */
function isPlainObject(val) {
  if (toString.call(val) !== '[object Object]') {
    return false;
  }

  var prototype = Object.getPrototypeOf(val);
  return prototype === null || prototype === Object.prototype;
}

/**
 * Determine if a value is a Date
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Date, otherwise false
 */
function isDate(val) {
  return toString.call(val) === '[object Date]';
}

/**
 * Determine if a value is a File
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a File, otherwise false
 */
function isFile(val) {
  return toString.call(val) === '[object File]';
}

/**
 * Determine if a value is a Blob
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Blob, otherwise false
 */
function isBlob(val) {
  return toString.call(val) === '[object Blob]';
}

/**
 * Determine if a value is a Function
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Function, otherwise false
 */
function isFunction(val) {
  return toString.call(val) === '[object Function]';
}

/**
 * Determine if a value is a Stream
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Stream, otherwise false
 */
function isStream(val) {
  return isObject(val) && isFunction(val.pipe);
}

/**
 * Determine if a value is a URLSearchParams object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a URLSearchParams object, otherwise false
 */
function isURLSearchParams(val) {
  return typeof URLSearchParams !== 'undefined' && val instanceof URLSearchParams;
}

/**
 * Trim excess whitespace off the beginning and end of a string
 *
 * @param {String} str The String to trim
 * @returns {String} The String freed of excess whitespace
 */
function trim(str) {
  return str.replace(/^\s*/, '').replace(/\s*$/, '');
}

/**
 * Determine if we're running in a standard browser environment
 *
 * This allows axios to run in a web worker, and react-native.
 * Both environments support XMLHttpRequest, but not fully standard globals.
 *
 * web workers:
 *  typeof window -> undefined
 *  typeof document -> undefined
 *
 * react-native:
 *  navigator.product -> 'ReactNative'
 * nativescript
 *  navigator.product -> 'NativeScript' or 'NS'
 */
function isStandardBrowserEnv() {
  if (typeof navigator !== 'undefined' && (navigator.product === 'ReactNative' ||
                                           navigator.product === 'NativeScript' ||
                                           navigator.product === 'NS')) {
    return false;
  }
  return (
    typeof window !== 'undefined' &&
    typeof document !== 'undefined'
  );
}

/**
 * Iterate over an Array or an Object invoking a function for each item.
 *
 * If `obj` is an Array callback will be called passing
 * the value, index, and complete array for each item.
 *
 * If 'obj' is an Object callback will be called passing
 * the value, key, and complete object for each property.
 *
 * @param {Object|Array} obj The object to iterate
 * @param {Function} fn The callback to invoke for each item
 */
function forEach(obj, fn) {
  // Don't bother if no value provided
  if (obj === null || typeof obj === 'undefined') {
    return;
  }

  // Force an array if not already something iterable
  if (typeof obj !== 'object') {
    /*eslint no-param-reassign:0*/
    obj = [obj];
  }

  if (isArray(obj)) {
    // Iterate over array values
    for (var i = 0, l = obj.length; i < l; i++) {
      fn.call(null, obj[i], i, obj);
    }
  } else {
    // Iterate over object keys
    for (var key in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, key)) {
        fn.call(null, obj[key], key, obj);
      }
    }
  }
}

/**
 * Accepts varargs expecting each argument to be an object, then
 * immutably merges the properties of each object and returns result.
 *
 * When multiple objects contain the same key the later object in
 * the arguments list will take precedence.
 *
 * Example:
 *
 * ```js
 * var result = merge({foo: 123}, {foo: 456});
 * console.log(result.foo); // outputs 456
 * ```
 *
 * @param {Object} obj1 Object to merge
 * @returns {Object} Result of all merge properties
 */
function merge(/* obj1, obj2, obj3, ... */) {
  var result = {};
  function assignValue(val, key) {
    if (isPlainObject(result[key]) && isPlainObject(val)) {
      result[key] = merge(result[key], val);
    } else if (isPlainObject(val)) {
      result[key] = merge({}, val);
    } else if (isArray(val)) {
      result[key] = val.slice();
    } else {
      result[key] = val;
    }
  }

  for (var i = 0, l = arguments.length; i < l; i++) {
    forEach(arguments[i], assignValue);
  }
  return result;
}

/**
 * Extends object a by mutably adding to it the properties of object b.
 *
 * @param {Object} a The object to be extended
 * @param {Object} b The object to copy properties from
 * @param {Object} thisArg The object to bind function to
 * @return {Object} The resulting value of object a
 */
function extend(a, b, thisArg) {
  forEach(b, function assignValue(val, key) {
    if (thisArg && typeof val === 'function') {
      a[key] = bind(val, thisArg);
    } else {
      a[key] = val;
    }
  });
  return a;
}

/**
 * Remove byte order marker. This catches EF BB BF (the UTF-8 BOM)
 *
 * @param {string} content with BOM
 * @return {string} content value without BOM
 */
function stripBOM(content) {
  if (content.charCodeAt(0) === 0xFEFF) {
    content = content.slice(1);
  }
  return content;
}

module.exports = {
  isArray: isArray,
  isArrayBuffer: isArrayBuffer,
  isBuffer: isBuffer,
  isFormData: isFormData,
  isArrayBufferView: isArrayBufferView,
  isString: isString,
  isNumber: isNumber,
  isObject: isObject,
  isPlainObject: isPlainObject,
  isUndefined: isUndefined,
  isDate: isDate,
  isFile: isFile,
  isBlob: isBlob,
  isFunction: isFunction,
  isStream: isStream,
  isURLSearchParams: isURLSearchParams,
  isStandardBrowserEnv: isStandardBrowserEnv,
  forEach: forEach,
  merge: merge,
  extend: extend,
  trim: trim,
  stripBOM: stripBOM
};


/***/ }),

/***/ "./resources/js/app.js":
/*!*****************************!*\
  !*** ./resources/js/app.js ***!
  \*****************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

var _require = __webpack_require__(/*! ./work_js/ptcmrd */ "./resources/js/work_js/ptcmrd.js"),
    findMobile = _require.findMobile;

var _require2 = __webpack_require__(/*! ./work_js/ptcmcb */ "./resources/js/work_js/ptcmcb.js"),
    clipboard = _require2.clipboard;

var _require3 = __webpack_require__(/*! ./work_js/ptcmta */ "./resources/js/work_js/ptcmta.js"),
    TreeAction = _require3.TreeAction;

var _require4 = __webpack_require__(/*! ./work_js/ptcmhb */ "./resources/js/work_js/ptcmhb.js"),
    hierarchyBar = _require4.hierarchyBar;

var _require5 = __webpack_require__(/*! ./work_js/ptcmtp */ "./resources/js/work_js/ptcmtp.js"),
    customToolTip = _require5.customToolTip;

__webpack_require__(/*! ./bootstrap */ "./resources/js/bootstrap.js");

if (document.getElementById('receive_combobox')) {
  document.getElementById('receive_combobox').addEventListener('change', function () {
    if (document.getElementById('receive_combobox').value === '1') {
      document.getElementById('recieving_port_number').value = '995';
      document.getElementById('recieving_server').value = "pop3.muumuu-mail.com";
    } else if (document.getElementById('receive_combobox').value === '2') {
      document.getElementById('recieving_port_number').value = '993';
      document.getElementById('recieving_server').value = 'imap4.muumuu-mail.com';
    }
  });
}

/***/ }),

/***/ "./resources/js/bootstrap.js":
/*!***********************************!*\
  !*** ./resources/js/bootstrap.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

window._ = __webpack_require__(/*! lodash */ "./node_modules/lodash/lodash.js");
/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */
// import Echo from 'laravel-echo';
// window.Pusher = require('pusher-js');
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });

/***/ }),

/***/ "./resources/js/work_js/ptcmcb.js":
/*!****************************************!*\
  !*** ./resources/js/work_js/ptcmcb.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "clipboard": () => (/* binding */ clipboard)
/* harmony export */ });
//@var Clipboardクラス クリップボードクラス
var clipboard = function () {
  //@var string selectNodeId 選択しているノードのid
  var selectNodeId = null; //@var string selectNodeDir 選択しているノードディレクトリ

  var selectNodeDir = null; //@var string カレントノードのid

  var currentId = null; //@var string カレントノードのディレクトリ

  var currentDir = null; //選択する
  //@param string dir ノードのディレクトリ
  //@param string id ノードのid

  var select = function select(dir, id) {
    if (typeof dir === 'string' && typeof id === 'string') {
      //クリップボードのプロパティに代入する
      selectNodeDir = dir;
      selectNodeId = id;
    }
  }; //カレント
  //@param string dir カレントのディレクトリ
  //@param string id カレントのid


  var current = function current(dir, id) {
    if (typeof dir === 'string' && typeof id === 'string') {
      //カレントのデータを代入する
      currentDir = dir;
      currentId = id;
    }
  }; //選択したノードのディレクトリを返す


  var getSelectDir = function getSelectDir() {
    return selectNodeDir;
  }; //選択したノードのidを返す


  var getSelectId = function getSelectId() {
    return selectNodeId;
  }; //カレントのディレクトリを返す


  var getCurrentDir = function getCurrentDir() {
    return currentDir;
  }; //カレントのidを返す


  var getCurrentId = function getCurrentId() {
    return currentId;
  };

  return {
    select: select,
    current: current,
    getSelectDir: getSelectDir,
    getSelectId: getSelectId,
    getCurrentId: getCurrentId,
    getCurrentDir: getCurrentDir
  };
}();



/***/ }),

/***/ "./resources/js/work_js/ptcmhb.js":
/*!****************************************!*\
  !*** ./resources/js/work_js/ptcmhb.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "hierarchyBar": () => (/* binding */ hierarchyBar)
/* harmony export */ });
/* harmony import */ var _ptcmrd__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ptcmrd */ "./resources/js/work_js/ptcmrd.js");
 //var HIerarchyBarクラス パンくずリストクラス

var hierarchyBar = {};

if (_ptcmrd__WEBPACK_IMPORTED_MODULE_0__.findMobile.deviceName !== 'pc') {
  hierarchyBar = function (barInfo, projectionChain) {
    //@var object 作業管理システムのデータ階層
    var bar = {}; //@var object 投影のid

    var projectionInfo = {}; //@var dom パンくずリストのdom

    var barElement = null;

    if (document.getElementById('panlist')) {
      barElement = document.getElementById('panlist');
    } //@var string 次の表示リスト


    var next = ''; //パンくずリストのオブジェクトデータの作成

    var createBarinfo = function createBarinfo() {
      barInfo.forEach(function (block) {
        block.forEach(function (info) {
          //@var string 親要素の値
          var key = Object.keys(info)[0]; //"1.chaintree"と"0.notitle"は"作業管理システム"という名前に変える

          if (key === "1.chaintree") {
            key = "作業管理システム";
          }

          if (key === "0.notitle") {
            key = "作業管理システム";
          } //@var mix 子要素の値


          var value = Object.values(info)[0]; //"0.notitle"はループをコンティニューする

          if (value === "0.notitle") {
            return;
          } //barにkeyがキーとして登録されていれば


          if (key in bar) {
            bar[key].push(value);
          } else {
            bar[key] = [value];
          }
        });
      });
    };

    var createProjectionInfo = function createProjectionInfo() {
      projectionChain.forEach(function (info) {
        projectionInfo[Object.keys(info)[0]] = Object.values(info)[0];
      });
    }; //パンくずリストのdom作成
    //@param array パンくずリストに表示する要素の配列


    var createBar = function createBar(barInfo) {
      barInfo.forEach(function (info) {
        createBarElement(info, bar[info]);
      });
    }; //リストの1つのdomを作成する
    //@param string info 表示する要素の情報
    //@param array barinfo infoの子要素


    var createBarElement = function createBarElement(info, barInfo) {
      //タイトルの作成
      barElement.appendChild(createTitleElement(info)); //子要素があれば、次の選択の要素を作成する

      if (barInfo) {
        barElement.appendChild(createNextElement(barInfo));
      }
    }; //タイトルのdomの作成
    //@param string タイトルの情報


    var createTitleElement = function createTitleElement(info) {
      //@var dom タイトルのdiv要素
      var titleDiv = document.createElement('div');

      if (info === next) {
        titleDiv.classList.add('current_bar');
      } //表示するタイトルを代入する


      if (info !== '作業管理システム') {
        titleDiv.innerText = info.split('.')[1];
        titleDiv.addEventListener('click', {
          info: info,
          handleEvent: pageMove
        });
      } else {
        titleDiv.innerText = '作業管理システム';
      }

      return titleDiv;
    };

    var createNextElement = function createNextElement(info) {
      //@var dom 次のリストのdom
      var nextDiv = document.createElement('div'); //@var dom >の矢印のdom

      var nextClickDiv = document.createElement('div'); //'>'を代入する

      nextClickDiv.innerText = '>'; //マウスを合わせた時にポインターを表示する(pcの場合の確認のため)

      nextClickDiv.style.cursor = 'pointer'; //マージンの設定

      nextClickDiv.style.margin = '0 5px'; //@var boolean 表示、非表示の判断

      var clickon = false; //クリック処理(仮)

      nextClickDiv.addEventListener('click', function () {
        if (clickon === true) {
          ul.classList.add('notdisplaylist');
          clickon = false;
        } else {
          ul.classList.remove('notdisplaylist');
          clickon = true;
        }
      }); //@var dom 遷移リストを表示するdom

      var ul = document.createElement('ul'); //cssを加える

      ul.classList.add('barlist');
      ul.classList.add('notdisplaylist');
      info.forEach(function (info) {
        //@var dom 遷移する要素、クリックして移動する
        var li = document.createElement('li'); //タイトルを代入する

        li.innerText = info.split('.')[1];

        if (info.split('.')[0].substr(0, 2) === 'ta') {
          li.classList.add('projection_bar');
        } //移動のクリック処理


        li.addEventListener('click', {
          info: info,
          handleEvent: pageMove
        }); //pc確認のためのポインター

        li.style = 'cursor: pointer;';
        ul.appendChild(li);
      });
      nextDiv.appendChild(nextClickDiv);
      nextDiv.appendChild(ul);
      return nextDiv;
    }; //ページ移動


    var pageMove = function pageMove() {
      //次の要素を保存する
      next = this.info;
      var id = this.info.split('.')[0];

      if (id === 'sslg') {
        //ログ確認の場合
        window.location = document.location.origin + '/pslg';
      } else if (id === 'ssnw') {
        window.location = document.location.origin + '/psnw01';
      } else if (id.substr(0, 2) === 'ji' || id.substr(0, 2) === 'bs') {
        //@var string Laravelのセッションid
        var clientId = document.getElementById('hidden_client_id').value; //@var string ノードのid

        var nodeId = id; //移動命令

        window.location = document.location.origin + "/show/".concat(clientId, "/").concat(nodeId);
      } else if (id.substr(0, 2) === 'kb') {
        //@var string Laravelのセッションid
        var _clientId = document.getElementById('hidden_client_id').value; //@var string ノードのid

        var _nodeId = id;

        if (_nodeId === 'kb') {
          //移動命令
          window.location = document.location.origin + "/pskb/";
        } else {
          //移動命令
          window.location = document.location.origin + "/pskb/show/".concat(_clientId, "/").concat(_nodeId);
        }
      } else if (id.substr(0, 2) === 'sb') {
        //@var string Laravelのセッションid
        var _clientId2 = document.getElementById('hidden_client_id').value; //@var string ノードのid

        var _nodeId2 = id;

        if (_nodeId2 === 'sb') {
          //移動命令
          window.location = document.location.origin + "/pssb01/";
        } else {
          //移動命令
          window.location = document.location.origin + "/pssb01/show/".concat(_clientId2, "/").concat(_nodeId2);
        }
      } else if (id.substr(0, 2) === 'ta') {
        //@var string Laravelのセッションid
        var _clientId3 = document.getElementById('hidden_client_id').value; //@var string ノードのid

        var _nodeId3 = id; //@var string 投影元のid

        var projectionId = projectionInfo[_nodeId3]; //作業場所の場合

        if (projectionId.substr(0, 2) === 'sb') {
          window.location = document.location.origin + "/pssb01/show/".concat(_clientId3, "/").concat(_nodeId3);
        } else {
          //移動命令
          window.location = document.location.origin + "/show/".concat(_clientId3, "/").concat(_nodeId3);
        }
      } else if (id.substr(0, 2) === 'ss') {
        //@var string Laravelのセッションid
        var _clientId4 = document.getElementById('hidden_client_id').value; //@var string ノードのid

        var _nodeId4 = id; //システム設計への移動は、ログ画面に移動する

        if (_nodeId4 === 'ss') {
          //移動命令
          window.location = document.location.origin + "/pslg/";
        } else if (_nodeId4 === 'ssnw') {
          //移動命令
          window.location = document.location.origin + "/psnw01/";
        } else if (_nodeId4 === 'sslg') {
          //移動命令
          window.location = document.location.origin + "/pslg/";
        }
      }
    };

    var chain = [];

    var createChainBar = function createChainBar(title) {
      if (title !== '作業管理システム') {
        Object.keys(bar).forEach(function (key) {
          if (bar[key].includes(title)) {
            chain.push(title);
            createChainBar(key);
          }
        });
      } else {
        chain.push(title);
      }
    }; //パンくずリストに表示する次の要素を画面移動の前に保存する


    window.addEventListener('beforeunload', function () {
      localStorage.setItem('next', next);
    }); //変数barにパンくずリスト階層のオブジェクトデータを作成、代入する

    createBarinfo(); //投影データの作成

    createProjectionInfo(); //次の要素を取得する

    next = localStorage.getItem('next'); //@var string パス

    var pathName = document.location.pathname;

    if (pathName === '/') {
      next = 'bs00000001.部署A';
    } else if (pathName.split('/')[1] === 'show') {
      //@var string パスの最後のid
      var nextId = pathName.split('/')[3];
      barInfo.forEach(function (block) {
        block.forEach(function (info) {
          if (Object.keys(info)[0].split('.')[0] === nextId) {
            next = Object.keys(info)[0];
          }

          if (Object.values(info)[0].split('.')[0] === nextId) {
            next = Object.values(info)[0];
          }
        });
      });
    } //次の要素がない(初期表示の場合)


    if (!next) {
      next = 'bs00000001.部署A';
    } //リストに表示する階層の要素をchainに代入する


    createChainBar(next); //パンくずリストのdom作成

    createBar(chain.reverse());
  }(treeChain, projectionChain);
}



/***/ }),

/***/ "./resources/js/work_js/ptcmrd.js":
/*!****************************************!*\
  !*** ./resources/js/work_js/ptcmrd.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "findMobile": () => (/* binding */ findMobile)
/* harmony export */ });
//@var object モバイル端末かパソコンかを検出する 
var findMobile = {};

findMobile = function () {
  //@var string デバイスの名前(pc,smartphone,tablet)
  var deviceName; //@var boolean モバイルデバイスか(タッチ操作可能か、画面回転可能か)、どうか

  var isMobileDevice = false; //@var boolean user-agent文字にMobileが入っているか

  var isMobile = /Mobile/i.test(navigator.userAgent); //@var boolean タッチ操作可能か

  var hasTouchscreen = false; //タッチ可能かを検出する

  if ("maxTouchPoints" in navigator) {
    hasTouchscreen = navigator.maxTouchPoints > 0; //タッチ可能かを検出する
  } else if ("msMaxTouchPoints" in navigator) {
    hasTouchscreen = navigator.msMaxTouchPoints > 0;
  } else {
    //回転可能か調べる
    if ('orientation' in window) {
      hasTouchscreen = true;
    }
  } //mobileの文字があるか、タッチ可能かのどちらかで、trueにする


  if (isMobile || hasTouchscreen) {
    isMobileDevice = true;
  } //モバイル端末ではない場合、パソコン


  if (isMobileDevice === false) {
    deviceName = 'pc';
  } else {
    //モバイル端末の場合
    //横幅が420以下かつ縦幅が920以下の場合smartphone
    if (window.screen.width <= 420 && window.screen.height <= 920) {
      deviceName = 'smartphone';
    } else {
      //それ以外はtablet
      deviceName = 'tablet';
    }
  } //@var string デバイスの名前


  var device = localStorage.getItem('device');
  localStorage.setItem('device', deviceName);

  if (device === null && device === undefined && (deviceName === 'smartphone' || deviceName === 'tablet')) {
    window.location = document.location.origin + '/pa0001/responsible/set';
  } else if (device === 'pc' && (deviceName === 'smartphone' || deviceName === 'tablet')) {
    window.location = document.location.origin + '/pa0001/responsible/set';
  } else if ((device === 'smartphone' || device === 'tablet') && deviceName === 'pc') {
    window.location = document.location.origin + '/pa0001/responsible/reset';
  }

  return {
    deviceName: deviceName
  };
}();



/***/ }),

/***/ "./resources/js/work_js/ptcmta.js":
/*!****************************************!*\
  !*** ./resources/js/work_js/ptcmta.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "TreeAction": () => (/* binding */ TreeAction)
/* harmony export */ });
/* harmony import */ var _ptcmcb__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ptcmcb */ "./resources/js/work_js/ptcmcb.js");
/* harmony import */ var _ptcmrd__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ptcmrd */ "./resources/js/work_js/ptcmrd.js");
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }


 //@var TreeActionクラス 公開するクラス,ツリー機能クラス

var TreeAction = {};

if (_ptcmrd__WEBPACK_IMPORTED_MODULE_1__.findMobile.deviceName === 'pc') {
  //TreeActionの名前空間
  //ツリー作成クラス
  TreeAction.createTree = {}; //ノードのdomなどを作成するクラス

  TreeAction.node = {}; //ツリーの探索やcss名を決定するクラス

  TreeAction.chainparser = {}; //ノードクラス

  TreeAction.node = /*#__PURE__*/function () {
    //@param string dir ツリーのディレクトリ
    //@param int id ノードのid
    function Node(nodeDir, id) {
      _classCallCheck(this, Node);

      //@var string dir ツリーのディレクトリ
      this.dir = nodeDir; //@var string id データベースのid

      this.id = id; //@var string className htmlのcss名となる

      this.className; //@var string title dom要素に表示する文字

      this.title = nodeDir.split('/').pop(); //@var array child 子ノードクラスを格納する配列

      this.child = []; //@var dom element ノードのdom要素

      this.element = null; //@var array toLink 投影先のリンク、idとディレクトリ

      this.toLink = []; //@var array fromLink 投影元のリンク、idとディレクトリ

      this.fromLink = []; //@var boolean hide 隠蔽/表示を切り替える

      this.hide = false;
    } //ツリーノードのdomを作成する
    //@return dom node.element 作成したツリーのdom


    _createClass(Node, [{
      key: "createTree",
      value: function createTree() {
        var _this = this;

        //domを作成して、node.elementに代入する。
        this.createElement();

        if (this.child !== []) {
          //子要素がある子要素のdomを追加する
          this.child.forEach(function (child) {
            //Node.createTree(child)によって子要素のdomを作成しつつ、
            //返り値の子要素のdomを追加する
            _this.appendTree(child.createTree());
          });
        }

        return this.element;
      } //ノードのdom要素を生成する。

    }, {
      key: "createElement",
      value: function createElement() {
        //展開されるツリーのdom生成
        if (this.className === 'expandtree' || this.className === 'lastexpandtree') {
          this.createExpandTree();
        } else if (this.className === 'linetree') {
          //linetreeのdom生成
          this.createLineTree();
        } else {
          //expandtreeとlinetree以外のdom生成
          //secondtree,endtreeはlinetreeの単要素
          //first,normaltree,lastnormaltree,lastreeはexpandtreeの単要素
          this.createLeafTree();
        }
      } //expandtree系を作成

    }, {
      key: "createExpandTree",
      value: function createExpandTree() {
        //div要素を生成する。
        var div = this.createFirstdiv(); //展開するボタンのあるツリー生成、挿入する

        div.appendChild(this.createTreeBox()); //展開されるツリーを作成する
        //@var dom ul ul要素

        var ul = document.createElement('ul'); //@var dom li li要素

        var li = document.createElement('li'); //li要素をulに追加

        ul.appendChild(li); //クラスのdom要素のthis.elementフィールドに、データのdom要素を保存する。

        div.appendChild(ul);
        this.element = div;
      } //linetreeを生成する。

    }, {
      key: "createLineTree",
      value: function createLineTree() {
        //linetreeのdom生成
        //div要素の生成する
        var div = this.createFirstdiv();

        if (this.title === 'マイツリー') {
          div.innerText = 'マイツリー';
        }

        this.element = div;
      } //expandtree系やlinetree以外のdomを生成する

    }, {
      key: "createLeafTree",
      value: function createLeafTree() {
        //expandtreeとlinetree以外のdom生成
        //secondtree,endtreeはlinetreeの単要素
        //first,normaltree,lastnormaltree,lastreeはexpandtreeの単要素
        //@var dom ul ul要素
        var ul = document.createElement('ul'); //classの追加により、cssを適用させる

        ul.classList.add(this.className); //@var dom li li要素

        var li = document.createElement('li'); //segmentというdiv要素を加える

        li.appendChild(this.createSegment());
        ul.appendChild(li);
        this.element = ul;
      } //ノードクラスにdomを最後に追加
      //@param dom element 追加するdom要素

    }, {
      key: "appendTree",
      value: function appendTree(element) {
        //展開されるツリーのdom挿入
        if (this.className === 'expandtree' || this.className === 'lastexpandtree') {
          //@var dom li expandtree系の子要素のdomの最初親要素はli
          var li = this.element.children[1].children[0];
          li.appendChild(element);
        } else if (this.className === 'linetree') {
          //linetreeのdom挿入
          //linetree系は、div要素の並びなのでそのままdomを追加する
          this.element.appendChild(element);
        }
      } //ボックスのボタンとタイトルのdom要素を生成する。
      //@return dom divTreeBox ボックスのボタンとタイトルがあるdom要素

    }, {
      key: "createTreeBox",
      value: function createTreeBox() {
        //@var dom divTreeBox 返すdom要素
        //div要素の生成
        var divTreeBox = document.createElement('div'); //class名を付ける

        divTreeBox.classList.add('treebox'); //二つのdom要素を生成して、挿入する
        //expandboxは、プラス、マイナスのボタンがあるボックス
        //titleboxは、ボタンの横にあるタイトル

        divTreeBox.appendChild(this.createExpandBox());
        divTreeBox.appendChild(this.createTitleBox());
        return divTreeBox;
      } //expandboxの展開されるボックスの生成
      //@return dom div 展開されるボックスのdom

    }, {
      key: "createExpandBox",
      value: function createExpandBox() {
        //@var dom div divTreeBoxに追加するdom
        var div = document.createElement('div'); //boxValueの0番目に、クラス名

        div.classList.add('expandbox'); //boxValueの1番目に、表示する文字。

        div.innerText = '-'; //ボックスを展開するクリックイベントを付ける

        this.addExpandEvent(div);
        return div;
      } //@param dom div クリックイベントを付けるdom
      //クリックイベントを付ける

    }, {
      key: "addExpandEvent",
      value: function addExpandEvent(div) {
        var _this2 = this;

        //@param イベント　event
        div.addEventListener('click', function (event) {
          //@var dom box treeBoxのexpandboxのdom
          var box = _this2.element.children[0].children[0]; //プラス,マイナス文字を反転する。

          if (box.innerText === '+') {
            box.innerText = '-'; //ツリーのhtmlの表示に切り替える

            _this2.openDisplayChild();
          } else if (box.innerText === '-') {
            //子要素全体を非表示にする
            _this2.noneDisplayTree();
          }
        });
      } //titleboxの生成
      //@return dom div 展開ボックスのタイトルのdom

    }, {
      key: "createTitleBox",
      value: function createTitleBox() {
        //@var dom div divTreeBoxに追加するdom
        var div = document.createElement('div'); //boxValueの0番目に、クラス名

        div.classList.add('titlebox'); //titleboxにsegmentを追加する

        div.appendChild(this.createSegment());
        return div;
      } //segmentというclass名をdiv要素を作成する

    }, {
      key: "createSegment",
      value: function createSegment() {
        //@var dom imgとタイトルを格納する
        var segment = document.createElement('div'); //segmentというcss名を追加する

        segment.classList.add('segment'); //@var string アイコンの名前

        var img = this.getImg(); //div要素に、アイコンとタイトルを挿入する

        segment.innerHTML = img + this.title; //クリック処理を追加する

        segment.addEventListener('click', {
          node: this,
          handleEvent: this.displayDetail
        });
        return segment;
      } //詳細行に表示

    }, {
      key: "displayDetail",
      value: function displayDetail() {
        //@var array dirArray ディレクトリの文字を分割した配列
        var dirArray = this.node.dir.split('/'); //一番後ろの文字を削除

        dirArray.pop(); //親要素のタイトルを取得する

        var topDir = dirArray.pop(); //フォーカス

        this.node.focus(); //クリップボードに選択したノードクラスのidとディレクトリを保存する

        _ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.select(this.node.dir, this.node.id); //選択したノードがカレントノード

        _ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.current(_ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.getSelectDir(), _ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.getSelectId()); //選択したノードの親要素がマイツリーである場合は、本来のノードクラスまでツリーを開く

        if (topDir === "マイツリー") {
          //@var string id 選択したノードクラスのid
          var id = this.node.id; //@var Nodeクラス selectNode 本来のノードクラス

          var selectNode = this.node.prototype.chainparser.searchNodeId(id, this.node.prototype.tree); //本来のノードクラスまでツリーを開く

          selectNode.openBottomUpTree();
        } // alert(this.node.id);


        if (this.node.id === 'sslg') {
          //ログ確認の場合
          window.location = document.location.origin + '/pslg';
        } else if (this.node.id === 'ssnw') {
          window.location = document.location.origin + '/psnw01';
        } else if (this.node.id.substr(0, 2) === 'ji' || this.node.id.substr(0, 2) === 'bs') {
          //@var string Laravelのセッションid
          var clientId = document.getElementById('hidden_client_id').value; //@var string ノードのid

          var nodeId = this.node.id; //移動命令

          window.location = document.location.origin + "/show/".concat(clientId, "/").concat(nodeId);
        } else if (this.node.id.substr(0, 2) === 'kb') {
          //@var string Laravelのセッションid
          var _clientId = document.getElementById('hidden_client_id').value; //@var string ノードのid

          var _nodeId = this.node.id;

          if (_nodeId === 'kb') {
            //移動命令
            window.location = document.location.origin + "/pskb/";
          } else {
            //移動命令
            window.location = document.location.origin + "/pskb/show/".concat(_clientId, "/").concat(_nodeId);
          }
        } else if (this.node.id.substr(0, 2) === 'sb') {
          //@var string Laravelのセッションid
          var _clientId2 = document.getElementById('hidden_client_id').value; //@var string ノードのid

          var _nodeId2 = this.node.id;

          if (_nodeId2 === 'sb') {
            //移動命令
            window.location = document.location.origin + "/pssb01/";
          } else {
            //移動命令
            window.location = document.location.origin + "/pssb01/show/".concat(_clientId2, "/").concat(_nodeId2);
          }
        } else if (this.node.id.substr(0, 2) === 'ta') {
          //@var string Laravelのセッションid
          var _clientId3 = document.getElementById('hidden_client_id').value; //@var string ノードのid

          var _nodeId3 = this.node.id; //@var string 投影元のノードのid

          var projectionId = this.node.fromLink[0]; //作業場所の場合

          if (projectionId.substr(0, 2) === 'sb') {
            window.location = document.location.origin + "/pssb01/show/".concat(_clientId3, "/").concat(_nodeId3);
          } else {
            //移動命令
            window.location = document.location.origin + "/show/".concat(_clientId3, "/").concat(_nodeId3);
          }
        } else if (this.node.id.substr(0, 2) === "lo") {
          window.location = document.location.origin + '/logout';
        }
      } //選択したノードを太字にする

    }, {
      key: "focus",
      value: function focus() {
        //@var string beforeSelectId 前回選択したid
        var beforeSelectId = _ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.getCurrentId(); //@var Nodeクラス beforeSelect　前回選択したノードクラス

        var beforeSelect = this.prototype.chainparser.searchNodeId(beforeSelectId, this.prototype.tree); //前回選択したノードクラスのフォーカスを消す

        beforeSelect === null || beforeSelect === void 0 ? void 0 : beforeSelect.outFocusNode(); //@var array dirArray ディレクトリを分割した配列

        var dirArray = this.dir.split('/'); //一番後ろの文字を削除

        dirArray.pop(); //親要素のタイトルを取得する
        //@var string topDir 第一階層のタイトル

        var topDir = dirArray.pop();

        if (topDir === "マイツリー") {
          //@var string id マイツリーに登録した本来のツリーノードのid
          var id = this.id; //@var Nodeクラス selectNode 本来のノードクラス

          var selectNode = this.prototype.chainparser.searchNodeId(id, this.prototype.tree); //本来のノードをフォーカスする

          selectNode.onFocusNode();
        } else {
          //選択したノードクラスをフォーカスする
          this.onFocusNode();
        }
      } //フォーカスする(選択したタイトルを太字にする)

    }, {
      key: "onFocusNode",
      value: function onFocusNode() {
        //div要素の場合とul要素の場合を分ける
        if (this.element.nodeName === 'DIV') {
          this.element.children[0].children[1].classList.add('focus');
        } else {
          this.element.children[0].classList.add('focus');
        }
      } //フォーカスを外す(選択したタイトルの太字を戻す)

    }, {
      key: "outFocusNode",
      value: function outFocusNode() {
        //div要素の場合
        if (this.element.nodeName === 'DIV') {
          this.element.children[0].children[1].classList.remove('focus');
        } else {
          this.element.children[0].classList.remove('focus');
        }
      } //投影先のタイトルを斜体にする

    }, {
      key: "onSync",
      value: function onSync() {
        //div要素の場合
        if (this.element.nodeName === 'DIV') {
          this.element.children[0].children[1].classList.add('sync');
        } else {
          this.element.children[0].classList.add('sync');
        }
      } //目的のノードクラスまでツリーを開く
      //@var Nodeクラス tree ツリーの全体のインスタンス

    }, {
      key: "openBottomUpTree",
      value: function openBottomUpTree() {
        //@var dom box treeBoxのexpandboxのdom
        var box = this.element.children[0].children[0]; //@var array splitDir ディレクトリの文字を分割した配列

        var splitDir = this.dir.split('/'); //最後の文字を削除する

        splitDir.pop(); //配列が1の時は、chaintreeなので、openBottomUpTreeを呼ばない

        if (splitDir.length !== 1) {
          //@var Nodeクラス palent 目的のノードクラスの親要素
          var palent = this.prototype.chainparser.searchNodeDir(splitDir.join('/'), this.prototype.tree); //目的のノードクラスの親要素のツリーを開く
          //親要素が閉じているなら開く

          if (palent.element.children[0].children[0].innerText === "+") {
            //目的のノードクラスの親要素のツリーを開く
            palent.openBox();
            palent.openDisplayChild(); //また親要素を引数にして再帰的に、openBottomUpTreeを呼び出す

            palent.openBottomUpTree();
          }
        }
      } //目的のノードクラスまでツリーを開く
      //@var Nodeクラス tree ツリーの全体のインスタンス

    }, {
      key: "openBottomUpTreePageMove",
      value: function openBottomUpTreePageMove() {
        //@var dom box treeBoxのexpandboxのdom
        var box = this.element.children[0].children[0]; //@var array splitDir ディレクトリの文字を分割した配列

        var splitDir = this.dir.split('/'); //最後の文字を削除する

        splitDir.pop(); //子ノードを開く

        this.openBox();
        this.openDisplayChild(); //配列が1の時は、chaintreeなので、openBottomUpTreeを呼ばない

        if (splitDir.length !== 1) {
          //@var Nodeクラス palent 目的のノードクラスの親要素
          var palent = this.prototype.chainparser.searchNodeDir(splitDir.join('/'), this.prototype.tree); //親要素が閉じているなら開く

          if (palent.element.children[0].children[0].innerText === "+") {
            //目的のノードクラスの親要素のツリーを開く
            palent.openBox();
            palent.openDisplayChild(); //また親要素を引数にして再帰的に、openBottomUpTreeを呼び出す

            palent.openBottomUpTree();
          }
        }
      } //現在のスパイラルでは使わない
      //ツリーを閉じる
      // closeBottomUpTree(){
      //   //@var array splitDir ディレクトリの文字を分割した配列
      //   let splitDir = this.dir.split('/');
      //   //最後の文字を削除する
      //   splitDir.pop();
      //   //配列が1の時は、chaintreeなので、openBottomUpTreeを呼ばない
      //   if(splitDir.length !== 1){
      //     //@var Nodeクラス palent 目的のノードクラスの親要素
      //     let palent = this.prototype.chainparser.searchNodeDir(splitDir.join('/'), this.prototype.tree);
      //     //目的のノードクラスの親要素のツリーを閉じる
      //     palent.closeBox();
      //     palent.noneDisplayNode();
      //     //また親要素を引数にして再帰的に、closeBottomUpTreeを呼び出す
      //     palent.closeBottomUpTree();
      //   } 
      // }
      //現在のスパイラルでは使わない
      //@param Nodeクラス node 非表示にするノードクラス
      //子要素全体を非表示にする

    }, {
      key: "noneDisplayTree",
      value: function noneDisplayTree() {
        //expandtree系のノードの場合
        if (this.className === 'expandtree' || this.className === 'lastexpandtree') {
          this.closeBox();
        } //子要素を非表示にする


        this.child.forEach(function (child) {
          child.noneDisplayNode();
          child.noneDisplayTree();
        });
      } //palentのchildのelementを非表示にする。

    }, {
      key: "noneDisplayNode",
      value: function noneDisplayNode() {
        //展開するツリーボックスの場合
        if (this.className === 'expandtree' || this.className === 'lastexpandtree') {
          //@var Nodes配列 childNodes 
          var childNodes = this.element.childNodes; //展開するボックスの子要素(treeboxなど)を表示、非表示にする

          for (var i = 0; i < childNodes.length; i++) {
            childNodes[i].classList.add('unexpand');
          }
        } else {
          //展開するツリーボックス以外のノードの場合
          this.element.classList.add('unexpand');
        }
      } //子要素のツリーを表示する

    }, {
      key: "openDisplayChild",
      value: function openDisplayChild() {
        this.child.forEach(function (child) {
          //隠蔽ではないノードなら画面に表示する
          if (child.hide === false) {
            child.openDisplayNode();
          }
        });
      } //ツリーのボックスを展開する

    }, {
      key: "openDisplayNode",
      value: function openDisplayNode() {
        //展開するツリーボックスの場合
        if (this.className === 'expandtree' || this.className === 'lastexpandtree') {
          //@var Nodes配列 childNodes 
          var childNodes = this.element.childNodes; //展開するボックスの子要素(treeboxなど)を表示、非表示にする

          for (var i = 0; i < childNodes.length; i++) {
            childNodes[i].classList.remove('unexpand');
          }
        } else {
          //展開するツリーボックス以外のノードの場合
          this.element.classList.remove('unexpand');
        }
      } //ボックスを開く

    }, {
      key: "openBox",
      value: function openBox() {
        //子要素があるなら
        if (this.child.length !== 0) {
          //@var dom box treeBoxのexpandboxのdom
          var box = this.element.children[0].children[0]; //プラス文字を反転する。

          if (box.innerText === '+') {
            box.innerText = '-';
          }
        }
      } //ボックスを閉じる

    }, {
      key: "closeBox",
      value: function closeBox() {
        //子要素があるなら
        if (this.child.length !== 0) {
          //@var dom box treeBoxのexpandboxのdom
          var box = this.element.children[0].children[0]; //マイナス文字を反転する。

          if (box.innerText === '-') {
            box.innerText = '+';
          }
        }
      } //expnadtreeやlinetreeの最初のdivタグを作成する
      //@return dom div classNameがcss名のdiv

    }, {
      key: "createFirstdiv",
      value: function createFirstdiv() {
        //var dom div 返すdiv
        var div = document.createElement('div');
        div.classList.add(this.className);
        return div;
      } //ノードのidから表示するsrcの値を取得する
      //@return string src

    }, {
      key: "getImg",
      value: function getImg() {
        //@var stirng 返すアイコンの名前
        var src = '';

        if (this.fromLink.length !== 0) {
          src = this.fromLink[0].substr(0, 2);
        } else {
          src = this.id.substr(0, 2);
        }

        var base64ImgList = {
          'back': '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',
          'bs': 'iVBORw0KGgoAAAANSUhEUgAAAOAAAADSCAIAAADc/VsQAAAABGdBTUEAALGPC/xhBQAAAAlwSFlzAAAOwwAADsMBx2+oZAAABURJREFUeF7t3b2KXVUYx+EZGcHGOxARJZ1Y2KXzA7yBkEJNFSwFK5s0IY2NlWApqaIWITcg+NGlsxC7oIh4BzaCA5MBl7CdvGTWcc3e+3/2PE/jKmYmZ7Y/Drwv6ySHJycnB5DqmfZfiCRQogmUaAIlmkCJJlCiWTNtzeHhYTvNbJlyvIMSTaBEEyjRBEo0gRItcYofnEP3fS8x+OsfXb/RTjM7vn+vnc4z8n/EOyjRBEo0gRJNoEQTKNEESrTl1kz925PBRUm5/ih/zf6XNNNTKl/AYnuiORz/8Us7TT182A4TnY/UOyjRBEo0gRJNoEQTKNFGp/jFZvNB5WhfvqQLn0OfYq+n+PpBlUzxbJVAiSZQogmUaAIlmkCJtsOaaXs3G0qrr5kOrl5th82wZmKrBEo0gRJNoEQTKNFWnuJ3uHDQr5oZy9H46IVX2mliySm+1H//JlG5gjDFs1UCJZpAiSZQogmUaAIl2ixrpv5NzRz+evRFO008d+WDdprqv5YxvGbqXx6Vr39f1M+5Ys3EFgiUaAIlmkCJJlCijU7x/QKH0x1G+3mm+MFnUr7+dX9m+e1/fvtmO008//b37TTx5CP1Dko0gRJNoEQTKNEESjSBEq1eM82xE9kXF37d4R/lIy33L8+++H47bcXfv3/ZThPWTGyBQIkmUKIJlGgCJZpAibbDmmmOnUjgfZzSyKLkKba3zrvwB+UdlGgCJZpAiSZQogmUaKOfSdredYdyDi2tPsWvuwOZabNxhndQogmUaAIlmkCJJlCiCZRoO6yZSnt93aF/UVIafHSn5lgzzcGaCWoCJZpAiSZQogmUaBuc4tOuO+yq/1LOupZ5UN5BiSZQogmUaAIlmkCJJlCiJa6ZBu9A9G+USquvmUrlc+7X/y8V9VvmmXgHJZpAiSZQogmUaAIl2ugUX+ofORe7A9E/sc6x1ljSdz981k4Tb73xUTtN9H/lYp58+N5BiSZQogmUaAIlmkCJJlCizbJm6je4kFpseVQqX3y5uxk3uCd679Y37TTx1SfvtNNE/8+8e/fDdpp46eUr7XSe33591E4TN29+3k7/8g5KNIESTaBEEyjRBEo0gRKtXjOteyXn6wcP2mni3WvX2mmi/ysXM75m6t8olQavIw3+Qf3fbs3EFgiUaAIlmkCJJlCi1dN6OR0PGhzDj67faKeJ4/v32mmi/2d+fPtOO028/tqr7XSeH3/6uZ0mPr1zu53+a3C67x+Zy690WQRmIVCiCZRoAiWaQIkmUKLNsmbq3yiVBm97DP5B/d++5Jqp1H8vpH/NVBrcKJWsmdgCgRJNoEQTKNEESjSXRc5yWaSdJvr/9FLnwH7KX2DLnhEo0QRKNIESTaBEEyjRllszlfrvhfSvmUqDG6WSyyLtdB5rJjZLoEQTKNEESjSBEu1SXxYZ/DUzp/jB2x6D90JKpng2S6BEEyjRBEo0gRJNoERzWeT/W33NVBrcKK34l4icsmZizwiUaAIlmkCJJlCi7fCPyQbeIFnsXkgpc4ovLTawl0zxbJZAiSZQogmUaAIlmkCJtvKaqTS4UVr3Xkj5PE+Vj3Td3dMcRjZKJe+gRBMo0QRKNIESTaBEEyjREtdMpcXuKJWsmTpZM3G5CJRoAiWaQIkmUKLtzRS/LlN8J1M8l4tAiSZQogmUaAIlmkCJJlCiCZRoAiWaQIkmUKIJlGgCJZpAiSZQogmUaAIlmkCJJlCi1Z9JKl2SDyqNf/yo5DNJZ/hMElsgUKIJlGgCJZpAiSZQogmUaAIlmkCJJlCiCZRoAiXYwcFjJESfW9VsV0IAAAAASUVORK5CYII=',
          'copy': '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',
          'delete': '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',
          'insert': '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',
          'ji': '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aYQIQgQhggozguhEiODhDoQIQgQhgtS6UGFGiCBERAq1ESJOaYQIQgQhggozguhEiODhDoQIQgQhgtS6UGFGiCBERAq1ESJa41E0x38JDaQGLprQym7w15In3fneLzU89Gv/0ECIeOcn//308L3ww8/8XQP/zwgRypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERypIRIpojRBAiUohIQexEarFEiCBEUAMoaV18LxAiCBERyoLxQYiHKnvlt/5oA/9kBynETqmJPSBEXPnQVxp+7A9fGhH/3AcRyoIRIuKhyggRhAg765Wa2ANCBCGCe4+CEBGhLBghIh6qjBBBiLCzXqmJPSBEECK49ygIERHKghEi4qHKCBGECDvrlZrYA0IEIYJ7j4IQEaEsGCEiHqqMEEGIsLNeqYk9IEQQIrj3KAgREcqCESLiocoIEYQIO+uVmtgDQgQhgnuPghARoSwYISIeqowQQYiws16piT0gRBAiuPcoCBERyoIRIuKhyggRhAg765Wa2ANCBCGCe4+CEBGhLBghIh6qjBBBiLCzXqmJPSBEECK49ygIERHKghEi4qHKCBGECDvrlZrYA0IEIYJ7j4IQEaEsGX/xiOYIEYQI/r0O/Nu/QRAiLv/y3zQ88dxLI+J/VAURSm3EVvJrNEeIIEQQIrj3KAgRhAjuPQpCRIRSG7GV/BrNESIIEYQI7j0KQgQhgnuPghARodRGbCW/RnOECEIEIYJ7j4IQQYjg3qMgREQotRFbya/RHCGCEEGI4N6jIEQQIrj3KAgREUptxFbyazRHiCBEECK49ygIEYQI7j0KQkSEUhuxlfwazREiCBGECO49CkIEIYJ7j4IQEaHURmwlv0ZzhAhCBCGCe4+CEEGI4N6jIEREKLURW8mv0RwhghBBiODeoyBEECK49ygI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',
          'last': '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',
          'lo': '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',
          'move': 'iVBORw0KGgoAAAANSUhEUgAAAOAAAADSCAIAAADc/VsQAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAbhSURBVHhe7d2/jxR1GMfxmdnZvVu5Y4FoYmzs7WwVf8TexFjZekaMomAE8VQCUUBBD6KYKInBv0A6GwsLLW2MiQmd9hbHcZfdY3/NCOapyPf7TGDY28/A+5VJeBKOvdvjnW/xZHcnnUwmSVxRFDYB85DZn4AkAoU0AoU0AoU0AoU0AoU0AoW0dDQa2RgynU5tAuaBExTSCBTSCBTSCBTSCBTSCBTSWDM1yb8nVmwK2bXQs2mHZWMbIjL3JZ0LH1+yKYQTFNIIFNIIFNIIFNIIFNIIFNIIFNLYgzbJ8PffbAoZ9bdsunMLhXdUTSZeJP0/f7EporvVsilk8dR5m0I4QSGNQCGNQCGNQCGNQCGNQCGNQCGNPWiTlPZnWOq96rJK4f1HX7v8tU0h4/W/bYpYbKU2hex+33twTlBII1BII1BII1BII1BII1BII1BIq7UHLT9/y6aI8TS3aWeVpbcxdP/ylizz9nazMxr1bYpY+vSyTXduXHhr0vbVP2wKuf7zDzaFLB36xqaIrfMHbQrZc/Q7m0I4QSGNQCGNQCGNQCGNQCGNQCGt1pqpf/ZdmyI6rx6xaWf5a6YkrdgizWfJdPO0WFy0KaLVe8SmkEHi3pq69J5W/uWHNoXkB7z/x6y3z6aIjS+8dSRrJjQYgUIagUIagUIagUIagUIagUJarT3ooGoP+tDqVzbtrOFwaFNIp9OxKSKtWpRqGkxu2BRSXjptU0jxxJM2hSw//ZJNQQvepyvetHHuDZtC2IOiwQgU0ggU0ggU0ggU0ggU0ggU0nT3oP5rOv0fO8+9tzu3WhV7O1n+72RjbdWmoEcftyFk9yveqjLLvYOscm3MHhT3LQKFNAKFNAKFNAKFNAKFNAKFNN09qP+aTn+X6e9BZZXDig+GvH7R23Tu2h7YFFIe9+72UmTee+r9H6ubVPy2tz953aaQyYlvbQrhBIU0AoU0AoU0AoU0AoU0AoW0ea6ZKj4k0dXQdwb7Bn/9alNE66crNoUMj5y1KWQ579oUMk29NVPhfrBjJ2nbFDE+9Y5NIfmqd5MQTlBII1BII1BII1BII1BII1BII1BIuz8/fnGOxon3G2ttr9sUsnnhpE0R6bHPbArptffYFOQunTevePczLje9lz72Vt60KaIovDVqlnmnJCcopBEopBEopBEopBEopBEopBEopNXag26ePWxTxO5V762uTTW2P4M2/NdGnn7PhpB8/7M2RSw8/6JNIe3Eeyt2mUxsCun/+L1NIeNN79/uXfFe7lkTJyikESikESikESikESikESikESik1dqDXj9XsQftfXAf7kHLsbcIvXHmqE0h13rLNoU8dtC7Y/YtFfcR99XZg3pPec/KIZtmgBMU0ggU0ggU0ggU0ggU0ggU0mqtmdbXvBeP3bTv6AWbGqUsvWfdXztuU0h7cM2mkIWPvI8aTNoVn2NYD2sm4F4jUEgjUEgjUEgjUEgjUEgjUEirtQftV3384q5mfvzisPA+qrBYe9umkNGxizaFLCfe3XOy2Z4X7EGBe41AIY1AIY1AIY1AIY1AIY1AIY3b0AQU29s2hWx0vVdtLiW5TSG13jZcF3tQ4F4jUEgjUEgjUEgjUEgjUEgjUEgj0ICs23WufUnuXJ3/l52xa47KpHSu6bTrXGWrdC77BrNBoJBGoJBGoJBGoJBGoJBGoJDW1EAL18hlD/GgKVve1V1yrnI8dS57/NngBIU0AoU0AoU0AoU0AoU0AoU0AoU03UBLly08I9ou+6I4+wmaxn41EaPR2Lny4dS5sk7LuezbzwYnKKQRKKQRKKQRKKQRKKQRKKQRKKQ1NdDMZau/CHuIBrInEDFxtVqlc5XpunMlk9S7ZokTFNIIFNIIFNIIFNIIFNIIFNIIFNKaGqit/iLsiyJsWRpnX6fHnl7E1JW77Bvo4QSFNAKFNAKFNAKFNAKFNAKFNN1AU5cthO6KfYMGsicQ0XHZQzQNJyikESikESikESikESikESikESikzTZQex3YXbGHiLB1aIR90QPG1qERt93e+PbLZd9gHjhBIY1AIY1AIY1AIY1AIY1AIY1AIS31b089nXo3W946c9imiNb+F2wKydwFm+wyc5LMai+YphXnRavGSrIovH87/eeqTSHj5GGbQva+dsCmGeAEhTQChTQChTQChTQChTQChTQChbRae9DJqWM2RQzysU1BzdyDJlXbyrtWVm1Y0zovzXRfJptl3k3Is2detimk+9RzNs0AJyikESikESikESikESikESik1VozVSqn3vKiocqZLcDSGkukutwnlc7vMys5QSGNQCGNQCGNQCGNQCGNQCGNQCEtne+H6wGeJPkP7QEn7JCItQ4AAAAASUVORK5CYII=',
          'new': 'iVBORw0KGgoAAAANSUhEUgAAAOAAAADSCAIAAADc/VsQAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAUsSURBVHhe7d2/q09xHMdxX9ePQSYTZVE2pWtwr5+b0Wq1+R/8AzcmKUoKJYuBGK2EsCtlwoDuqiT3Xnd4n/rUOXF/fN3v6/J4LF4DxfXsDO++99zRz58/t/QsLi7WgonaWr9CJIESTaBEEyjRBEo0gRJt9OPHj5qNhYWFWo3RaFSLf8XS0lKtVJ6gRBMo0QRKNIESTaBEEyjRBEq04Tvo4GfwTpw4Uasz+GeJtWPHjlqda9eu1WocOXKkVgBPUKIJlGgCJZpAiSZQogmUaOs6M7148aIWm8H09HStzq1bt2o1nJlgpQRKNIESTaBEEyjRBEo0gRJNoEQTKNEESjSBEk2gRBMo0QRKNIESTaBEEyjRBEo0gRJNoEQTKNEESjSBEk2gRBMo0QRKNIESTaBEEyjRBEo0gRJNoEQTKNEESjSBEm2S76ifmZmp1ZiamqpFzzq/4N5RD2MmUKIJlGgCJZpAiSZQovlRiP8RZyYYM4ESTaBEEyjRBEo0gRJNoEQTKNEESjSBEk2gRBMo0QRKNIESTaBEEyjRBEo0gRJNoEQTKNEESjSBEk2gRBMo0QRKNIESTaBEEyjRBEq0Sb7d7v3797Ua8/PzteiZnZ2ttSbebgdjJlCiCZRoAiWaQIkmUKJN8sx0/fr1Wo0PHz7Uoufy5cu11sSZCcZMoEQTKNEESjSBEk2gRPOzOv8jhw8frtW5e/durUb/t02QJyjRBEo0gRJNoEQTKNEESjRnJqJ5ghJNoEQTKNEESjSBEk2gRBMo0dxBN73Bb/V8+PBhrcbU1FSt1du6deBZ9vjx41qNPXv21BoHT1CiCZRoAiWaQIkmUKIJlGjOTKEG39V/9uzZWo39+/fXaty/f79WYzQa1Vq9r1+/1moM/n0WFxdrNd68eVNrlTxBiSZQogmUaAIlmkCJJlCiTfLMdPTo0VqN9ZxCNoWXL1/WavT/1f2v9rLnz5/XaqznK/bt27dajV27dtVak7m5uVqNR48e1eq8fv261m95ghJNoEQTKNEESjSBEk2gRBMo0XzcLsLx48drdZ48eVKrsXv37lpr0v8g3LFjx2o1Xr16VWt8Tp06Vatz8ODBWo3bt2/X6niCEk2gRBMo0QRKNIESTaBEc2baaN+/f6/VOH36dK3OCj+NtioTPDNt27atVmfww5b9ojxBiSZQogmUaAIlmkCJJlCiOTNttIsXL9Zq3Lt3r1bn48ePtRqDJ6ozZ87U+pOlpaVancEXfa38VfaDf3zwW0/7Bi9czkxsMgIlmkCJJlCiCZRoAiWaM9NGu3PnTq3GpUuXanXevXtX608WFhZq/Un/KnTy5MlajXX+tw5eqfpvOHNm4l8gUKIJlGgCJZpAiSZQogmUaO6gG23wTDgzM1Or8ze+thP8rs63b9/W6pw/f75Wo/8jPT1BiSZQogmUaAIlmkCJJlCiOTNF6J97tm/fXqvx9OnTWmPSP28t+xtnpv4/8MGDB7Ua+/btq9XxBCWaQIkmUKIJlGgCJZpAibauMxNrcPPmzVqNQ4cO1erMzs7Wagx+A2f/E0CTNfg375+Zrly5Uuu3PEGJJlCiCZRoAiWaQIkmUKINn5lW/j4qNtK5c+dqNT59+lSrMXgo7F929u7dW6vx7NmzWo0bN27UavRfCbas/779ZQcOHKi1Sp6gRBMo0QRKNIESTaBEEyjRBEo0d9B/0+fPn2s1rl69Wqvz5cuXWo3p6elajQsXLtRq7Ny5s9Zf4wlKNIESTaBEEyjRBEqwLVt+ATPyglIv3b60AAAAAElFTkSuQmCC',
          'next': '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',
          'ng': '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',
          'ok': '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',
          'open': 'iVBORw0KGgoAAAANSUhEUgAAAOAAAADSCAIAAADc/VsQAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAASpSURBVHhe7d0/TlR7HMZhhsRCKky4JZEN4C5cgZV0sAZCZgdDNQ0JHRugMjaswcZlaEIgl0aKCX8uxq/mnJwZ7oCOvMM8T6Fv8sshxnw8CSdzsLe0tHR7e3v3KwTqqZNky/U7ROpdXV3VbLi5uakFT+HFixc/hjso0QRKNIESTaBEEyjRBEq03mg0qtlwcHBQq2F/f78W/CF7e3u12nZ3d38Md1CiCZRoAiWaQIkmUKIJlGjjHzMNh8NaDf1+vxb8IYPBoFbbr9jcQYkmUKIJlGgCJZpAiSZQos3qMdPLly9rNXz+/LlW2+vXr2v91Ot9f2Gfh5r0Evmkv8/pXzr/+vVrrYY3b97Uavv27Vut/+MxE/NNoEQTKNEESjSBEk2gRBMo0ebs43aHh4e12ra3t2sxzsePH2u1vX//vtZPf/nnxnkOynwTKNEESjSBEk2gRBMo0Rburc5Xr17Vavvy5UutpzPpw2/r6+u1Gs7Pz2vNM4+ZmG8CJZpAiSZQogmUaAsX6L8T1PGT6k1w9w17V13z3LmDEk2gRBMo0QRKNIESTaBEEyjRBEo0gRJNoEQTKNEESjSBEk2gRBNouQ1Wf8SFJFCiCZRoAiWaQIkmUKIJlGgCJZpAiSZQogmUaAIlmkCJJlCiCfQ+9cO7OuqY2RMo0QRKNIESTaBEEyjRBEo0gd6nXlrrqGNmT6BEEyjRBEo0gRJNoEQTKNEESjSBEk2gRBMo0QRKNIESTaBEE+h96h25jjpm9gRKNIESTaBEEyjRBEo0gRKtNxqNajYMh8NaDf1+v1aey8vLWg2np6e1GjY2Nmq1jf0KIVZWVmo1fPr0qVbD5uZmrbaxXyHBYDCo1fYrNndQogmUaAIlmkCJJlCiCZRoAiXaM3kOurq6Wqvh4uKi1hTm7jnoWGtra7Xazs7OaoXxHJT5JlCiCZRoAiWaQIn2TAK9+4a9q85+Q70j11HHee6+Wx+rjueQOyjRBEo0gRJNoEQTKNEESjSBEk2gRBMo0QRKNIESTaBEEyjRBEo0gd7ndoI6ZvYESjSBEk2gRBMo0QRKNIESTaBEEyjRBEo0gRJNoEQTKNEESjSBlvqxYI9VX6WjjqdT19AgUKIJlGgCJZpAiSZQogmUaAJ9jHp3rqEeFHXUBR11WVtd01HXLCSBEk2gRBMo0QRKNIESTaBEEyjRBFrqUeR06vlkQx1MrS5rq7OO+iMuJIESTaBEEyjRBEo0gRJt4QJdnaCO2+q76446nkJd0FHHbXXW8c84dc1z5w5KNIESTaBEEyjRBEo0gRKtNxqNajYMh8NaDf1+v9bTefv2ba22Dx8+1GpYXp7VP7/uBzh6D3y1bexHQB76Rbqur69rte3s7NRqOz4+rvVEBoNBrbZfsbmDEk2gRBMo0QRKNIESTaBEEyjR/upz0KOjo1ptW1tbtfg9s3vD7uTkpFbDu3fvaj2W56DMN4ESTaBEEyjRBEo0gRJtzj5uxzPjMRPzTaBEEyjRBEo0gRJNoEQbH+j1OHUGf0611VHH7qCEEyjRBEo0gRJNoEQTKNG+/+9mNSGPOyjRBEo0gRJNoEQTKNEESjSBEmxp6T97U6Ppr5ppxQAAAABJRU5ErkJggg==',
          'remove': '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',
          'road': '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',
          'search': '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',
          'ss': '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',
          'tizu': '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',
          'top': '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',
          'tree': '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',
          'under': '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',
          'updown': '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',
          'ur': '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'
        };

        if (src === 'lo') {
          //ログアウトの場合
          src = 'ss';
        } else if (src === 'kb') {
          //システム設計の場合
          src = 'ss';
        } else if (src === 'sb') {
          //システム設計の場合
          src = 'tizu';
        }

        src = "<img width = \"15\" length = \"17\" src = \"data:image/png;base64,".concat(base64ImgList[src], "\">");
        return src;
      }
    }]);

    return Node;
  }(); //Chainparserクラス
  //@return ChainParserクラス チェインパーサーのクラス


  TreeAction.chainparser = function () {
    //一番上だけのclassNameを決定する。
    //linetreeか、expandtreeか決定する
    //子要素に子要素がなければ、linetree
    //それ以外は、expnadtree
    //@param Nodeクラス topNode 一番上のノード
    var decisionTreeClass = function decisionTreeClass(topNode) {
      var isExpand = false;

      if (topNode.id !== '0') {
        isExpand = true;
      } //子要素があれば、展開されるボックスのあるツリー(expandtree)


      if (isExpand === true) {
        topNode.className = 'expandtree';
      } else if (isExpand === false) {
        topNode.className = 'linetree';
      }
    }; //同じノードオブジェクトか比較。
    //@param Nodeクラス node 比較される
    //@param Nodeクラス compairNode 比較する
    //@return boolean 結果
    //idが同じであれば、同じノード


    var isEqual = function isEqual(node, compairNode) {
      if (node.id !== compairNode.id) {
        return false;
      }

      return true;
    }; //子要素のクラス名を決定していく。
    //@param Nodeクラス node 階層のノード
    //@param Nodeクラス palent nodeの親ノード


    var decisionChildClass = function decisionChildClass(node, palent) {
      //linetreeの場合
      if (node.className === 'linetree') {
        //メソッドの入力のノードと親ノードの最後のノードが等しければ
        if (isEqual(node, palent.child[palent.child.length - 1]) === true) {
          decisionLineTreeClass(node, true);
        } else {
          decisionLineTreeClass(node, false);
        } //expandtree系の場合

      } else if (node.className === 'expandtree' || node.className === 'lastexpandtree') {
        //親ノードがツリーの一番上chaintreeの場合
        if (palent.dir === '') {
          //メソッドの入力のノードと親ノードの最後のノードが等しければ、最後のノード。
          if (isEqual(node, palent.child[palent.child.length - 1]) === true) {
            node.className = 'lastexpandtree';
          } else {
            node.className = 'expandtree';
          } //nodeのcssを決定していく。


          decisionExpandTreeClass(node);
        } else {
          //palentからcssを作り直し。
          decisionExpandTreeClass(palent);
        }
      }
    }; //lientreeの子要素のクラスの名前を決める


    var decisionLineTreeClass = function decisionLineTreeClass(node, isLastFlag) {
      for (var i = 0; i < node.child.length; i++) {
        //全体の中の最後のツリーでかつ子要素の最後はendtree
        if (isLastFlag === true && i === node.child.length - 1) {
          node.child[i].className = 'endtree';
        } else {
          node.child[i].className = 'secondtree';
        }
      }
    }; //expnadtreeの子要素のクラス名を決める


    var decisionExpandTreeClass = function decisionExpandTreeClass(node) {
      //上から下にツリークラスを決定していく
      for (var i = 0; i < node.child.length; i++) {
        //expnadtreeの最初のツリー
        if (i === 0) {
          //子要素の子要素が0より多ければ、展開されるので、expandtree
          if (node.child[i].child.length > 0) {
            //子要素が1であるならば展開されるexpandtreeが最後
            if (node.child.length === 1) {
              node.child[i].className = 'lastexpandtree';
            } else {
              node.child[i].className = 'expandtree';
            }
          } else {
            //子要素の子要素がなければ、子要素は展開されない
            if (i === node.child.length - 1) {
              //最初の子要素でかつ、要素がひとつなら
              node.child[i].className = 'lastnormaltree';
            } else {
              //最初の要素で、続きがあるなら
              node.child[i].className = 'firsttree';
            }
          } //最後の要素かつ i が0以上

        } else if (i === node.child.length - 1 && i > 0) {
          if (node.child[i].child.length > 0) {
            //展開されるツリーの場合
            node.child[i].className = 'lastexpandtree';
          } else {
            //nodeの子要素がひとつだけではなくかつ最後の子要素の場合
            node.child[i].className = 'lasttree';
          }
        } else {
          //最初の子要素でも最後の子要素でもない
          if (node.child[i].child.length > 0) {
            //展開されるツリーの場合
            node.child[i].className = 'expandtree';
          } else {
            //nodeの子要素がひとつだけではなくかつ最後の子要素の場合
            node.child[i].className = 'normaltree';
          }
        } //展開するボックスについて再帰的に、決定していく


        if (node.child[i].child.length !== 0) {
          //子要素があるなら、子要素のクラス名を決定していく。
          decisionExpandTreeClass(node.child[i]);
        }
      }
    }; //@param string nodeDir ノードクラスのディレクトリ
    //@param Nodeクラス node chaintreeのノードクラス
    //@return Nodeクラス search 検索したノードクラス
    //ノードのディレクトリからノードクラスを探索


    var searchNodeDir = function searchNodeDir(nodeDir, node) {
      //ツリーの一番上chaintreeの場合
      //本来のchaintree.dirは '' 空文字
      //探索する時に.join('/')しているので、'/'を追加
      if (nodeDir === '/' || nodeDir === '') {
        return node;
      } //ノードクラスをnodoと子要素ごとに一列配列に展開する
      //@var array nodeArray 展開したノードクラスを格納する配列


      var nodeArray = concatNode(node); //@var Nodeクラス search 検索したノードを入れる

      var search;
      nodeArray.forEach(function (child) {
        if (child.dir === nodeDir) {
          search = child;
        }
      });
      return search;
    }; //idからノードクラスを検索する
    //@param string nodeId 探索するノードクラスのid
    //@param Nodeクラス node chaintreeのノードクラス
    //@return Nodeクラス search 検索したノードクラス


    var searchNodeId = function searchNodeId(nodeId, node) {
      //ノードクラスをnodoと子要素ごとに一列配列に展開する
      //@var array nodeArray 展開したノードクラスを格納する配列
      var nodeArray = concatNode(node); //var Nodeクラス search 検索したノードクラスを入れる

      var search;
      nodeArray.forEach(function (child) {
        if (child.id === nodeId) {
          search = child;
        }
      });
      return search;
    }; //現在のスパイラルでは使わない
    //ディレクトリとidからNodeクラスを検索する
    //@param string nodeDir ノードのディレクトリ
    //@param string id ノードクラスのid
    //@param Nodeクラス node 検索されるノードクラス(インスタンスのtree)
    // let searchNodeDirId = function searchNodeDirId(nodeDir, nodeId, node){
    //   //@var array nodeArray 展開したノードクラスを格納した配列
    //   let nodeArray = concatNode(node);
    //   //@var Nodeクラス search 検索するノードを代入する
    //   let search;
    //   nodeArray.forEach(child =>{
    //     //ノードクラスのディレクトリとidが等しかったら
    //     if(child.dir === nodeDir && child.id === nodeId){
    //       search = child;
    //     }
    //   });
    //   return search;
    // }
    //現在のスパイラルでは使わない
    //@param Nodeクラス node 展開されるノード
    //@return array array ノードを展開した配列
    //ノードクラスを子要素も含めて一列展開する


    var concatNode = function concatNode(node) {
      //var array array 返値 初期値はnode
      var array = [node]; //子要素を取得する。
      //linetreeは除く。
      //子要素がないなら、returnする

      if (node.child.length > 0) {
        node.child.forEach(function (child) {
          //concatNodeの返り値はarrayなので、変数のarrayと返り値concatする事で、一列の配列になる
          array = array.concat(concatNode(child));
        });
      }

      return array;
    }; //ノードの親要素を返す
    //@param string nodeId 親要素を探すノードのid
    //@param Nodeクラス node ツリーノードのインスタンス
    //@return Nodeクラス 親ノード


    var searchPalentNode = function searchPalentNode(nodeId, node) {
      //@var Nodeクラス 親ノードを代入する
      var search = null; //@var Nodeクラス 子ノード

      var childNode = searchNodeId(nodeId, node); //ノード全体をループする

      concatNode(node).forEach(function (palent) {
        //子要素があるなら
        if (palent.child !== []) {
          palent.child.forEach(function (child) {
            //親要素の中に子要素があるか、調べる
            if (isEqual(child, childNode)) {
              search = palent;
            }
          });
        }
      }); //結果を返す

      return search;
    }; //@param Nodeクラス fromNode 投影元のノード
    //@param Nodeクラス toNode 投影先のノード
    //投影元と投影先をリンクさせる


    var syncLink = function syncLink(fromNode, toNode) {
      //リンク元にリンク先のディレクトリを入れる
      //リンク先にリンク元のディレクトリを入れる
      fromNode.toLink.push(toNode.id);
      toNode.fromLink.push(fromNode.id);
    }; //隠蔽ノードを表示に替える
    //@var Nodeクラス child 表示するノード
    //@var Nodeクラス palent 表示するノードの親ノード


    var displayOpen = function displayOpen(child, palent) {
      //ノードを表示にする
      child.openDisplayNode(); //隣のノードのcss名を変更する
      //子ノードが親ノードの先頭にあるなら

      if (getIndexNodeOpen(child, palent) === 0) {
        //@var Nodeクラス 次のノードクラス
        var nextNode = getNextNode(child, palent);

        if (nextNode !== null) {
          //firsttreeならば、次のノードは、normaltree
          if (nextNode.element.className === 'firsttree') {
            nextNode.element.className = 'normaltree'; //lastnormalreeならば、次のノードは、最後なので、lasttree
          } else if (nextNode.element.className === 'lastnormaltree') {
            nextNode.element.className = 'lasttree';
          }
        } //子ノードが親ノードの最後にあるなら

      } else if (getIndexNodeOpen(child, palent) === getLengthChildOpen(palent)) {
        //@var Nodeクラス 次のノードクラス
        var backNode = getBackNode(child, palent);

        if (backNode !== null) {
          //lastexpandtreeならば、前のノードは、expandtreeになる
          if (backNode.element.className === 'lastexpandtree') {
            backNode.element.className = 'expandtree'; //lastnormaltreeならば、最初のtreeになる
          } else if (backNode.element.className === 'lastnormaltree') {
            backNode.element.className = 'firsttree'; //lasttreeならば、normaltreeとなる
          } else if (backNode.element.className === 'lasttree') {
            backNode.element.className = 'normaltree';
          }
        }
      } else {
        //中間はchildを元に戻す
        child.element.className = child.className;
      }
    }; //ノードを非表示にして、隣のノードの表示を変える
    //@var Nodeクラス child 非表示にするノード
    //@var Nodeクラス palent 非表示にするノードの親ノード


    var displayNone = function displayNone(child, palent) {
      //ノードを非表示にする
      child.noneDisplayNode(); //隣のノードのcss名を変更する
      //子ノードが親ノードの先頭にあるなら

      if (getIndexNodeHide(child, palent) === 0) {
        //@var Nodeクラス 次のノードクラス
        var nextNode = getNextNode(child, palent); //次ノードクラスがあるなら

        if (nextNode !== null) {
          //normaltreeならば、次のノードは、先頭になるので、firstree
          if (nextNode.element.className === 'normaltree') {
            nextNode.element.className = 'firsttree'; //lastreeならば、次のノードは、1つしかないので、lastnormaltree
          } else if (nextNode.element.className === 'lasttree') {
            nextNode.element.className = 'lastnormaltree';
          }
        } //子ノードが親ノードの最後にあるなら

      } else if (getIndexNodeHide(child, palent) === getLengthChild(palent)) {
        //@var Nodeクラス 次のノードクラス
        var backNode = getBackNode(child, palent); //後ろのノードクラスがあるなら

        if (backNode !== null) {
          //expandtreeならば、前のノードは、最後のexpandtreeになる
          if (backNode.element.className === 'expandtree') {
            backNode.element.className = 'lastexpandtree'; //firsttreeならば、最後のnormaltreeになる
          } else if (backNode.element.className === 'firsttree') {
            backNode.element.className = 'lastnormaltree'; //normaltreeならば、最後のtreeになる
          } else if (backNode.element.className === 'normaltree') {
            backNode.element.className = 'lasttree';
          }
        }
      }
    }; //hideを抜いたノードクラスのインデックス
    //@param Nodeクラス node インデックスを探すノードクラス
    //@param Nodeクラス palent 親クラス
    //@return int インデックス


    var getIndexNodeHide = function getIndexNodeHide(node, palent) {
      //@var int インデックス
      var index = 0;

      for (var i = 0; i < palent.child.length; i++) {
        //隠蔽していて、目的のノードクラスか、調べる
        if (palent.child[i].hide === true && isEqual(node, palent.child[i]) === true) {
          return index;
        } else if (palent.child[i].hide !== true) {
          //見つからないなら、インデックスを増やす
          index++;
        }
      }

      return index;
    }; //ノードクラスのインデックス
    //@param Nodeクラス node インデックスを探すノードクラス
    //@param Nodeクラス palent 親クラス
    //@return int インデックス


    var getIndexNodeOpen = function getIndexNodeOpen(node, palent) {
      //@var int インデックス
      var index = 0;

      for (var i = 0; i < palent.child.length; i++) {
        //隠蔽していて、目的のノードクラスか、調べる
        if (palent.child[i].hide === false && isEqual(node, palent.child[i]) === true) {
          return index;
        } else if (palent.child[i].hide !== true) {
          //見つからないなら、インデックスを増やす
          index++;
        }
      }

      return index;
    }; //hideを抜いた親クラスのchildの数を数える
    //@param Nodeクラス palent 数える親クラス
    //@return int childの数


    var getLengthChild = function getLengthChild(palent) {
      //@var int 数
      var length = 0;

      for (var i = 0; i < palent.child.length; i++) {
        //隠蔽していないchildを数える
        if (palent.child[i].hide === false) {
          length++;
        }
      }

      return length;
    }; //隠蔽から表示の時の子ノードの数を数える
    //@param Nodeクラス palent 親クラス
    //@return int 子ノードの数


    var getLengthChildOpen = function getLengthChildOpen(palent) {
      //@var int 数
      var length = 0;

      for (var i = 0; i < palent.child.length; i++) {
        //隠蔽していないchildを数える
        if (palent.child[i].hide === false) {
          length++;
        }
      }

      return length - 1;
    }; //hideを除いた次のノードクラスを取得する。
    //@param Nodeクラス child　次の基準となるノードクラス
    //@param Nodeクラス palent 親クラス
    //@return Nodeクラス 次のノードクラスなければ、null


    var getNextNode = function getNextNode(child, palent) {
      //@var int childのインデックス
      var index; //@var Nodeクラス 次のノードクラスを代入する

      var search = null; //childのインデックスを探す

      for (var i = 0; i < palent.child.length; i++) {
        if (isEqual(child, palent.child[i]) === true) {
          index = i;
        }
      } //indexの位置から、次のノードクラスを取得する(間の子ノードがhideがtrueでないノード)


      for (var _i = index + 1; _i < palent.child.length - 1; _i++) {
        if (palent.child[_i].hide === false) {
          search = palent.child[_i];
          break;
        }
      }

      return search;
    }; //hideを除いた後ろのノードクラスを取得する。
    //@param Nodeクラス child　次の基準となるノードクラス
    //@param Nodeクラス palent 親クラス
    //@return Nodeクラス 後ろのノードクラスなければ、null


    var getBackNode = function getBackNode(child, palent) {
      //@var int childのインデックス
      var index; //@var Nodeクラス 後ろのノードクラスを代入する

      var search = null; //childのインデックスを探す

      for (var i = 0; i < palent.child.length; i++) {
        if (isEqual(child, palent.child[i]) === true) {
          index = i;
        }
      }

      for (var _i2 = index - 1; _i2 >= 0; _i2--) {
        if (palent.child[_i2].hide === false) {
          search = palent.child[_i2];
          break;
        }
      }

      return search;
    };

    return {
      searchPalentNode: searchPalentNode,
      concatNode: concatNode,
      searchNodeId: searchNodeId,
      searchNodeDir: searchNodeDir,
      decisionChildClass: decisionChildClass,
      isEqual: isEqual,
      decisionTreeClass: decisionTreeClass,
      syncLink: syncLink,
      displayOpen: displayOpen,
      displayNone: displayNone
    };
  }(); //ツリーインスタンスとツリーのdomを作成する
  //@param array treesepalete 上下関係のオブジェクトのデータの配列
  //@param array projectionChain 投影データ
  //@param Nodeクラス Node ノードクラス
  //@param ChainParser chainparser チェインパーサーのクラス
  //@return Nodeクラス ツリーインスタンス


  TreeAction.createTree = function (treesepalete, projectionChain, Node, chainparser) {
    //ツリーの子要素のクラスを決定する。
    //@param Nodeクラス treeTop 第一階層のツリーノードクラス
    var decisionClass = function decisionClass(treeTop) {
      treeTop.child.forEach(function (node) {
        chainparser.decisionChildClass(node, treeTop);
      });
    }; //ツリーの子要素のdom要素を構築、追加していく。
    //@param Nodeクラス treeTop 第一階層のツリーノードクラス
    //@param dom fragment 仮のツリーのdom


    var createElement = function createElement(treeTop, fragment) {
      treeTop.child.forEach(function (node) {
        fragment.append(node.createTree());
      });
    }; //@param array sepalete 上下関係だけのツリーのデータ構造
    //@param Nodeクラス treeTop　ツリーの一番上の親ノードのクラス
    //@return Nodeクラス topNode 親ノードクラス


    var createTopNode = function createTopNode(sepalete, treeTop) {
      try {
        //@var Nodeクラス this.topNode ひとかたまりのツリーの一番上のNodeクラス
        var topNode; //topNodeを作成する
        //{'1.chaintree': '5.aa'}sepaleteのデータの中で、keyがchaintreeの値が一番上の親

        for (var i = 0; i < sepalete.length; i++) {
          if (String(Object.keys(sepalete[i])[0]) === '1.chaintree') {
            topNode = new Node(treeTop.dir + '/' + String(Object.values(sepalete[i])[0].split('.')[1]), String(Object.values(sepalete[i])[0].split('.')[0]));
            topNode.prototype = {
              chainparser: chainparser,
              tree: treeTop
            };
            delete sepalete[i];
          }
        } //データからクラスの階層構造を作成する。


        parse(topNode, sepalete); //Nodeクラスの第一階層のcss名を決定する。

        chainparser.decisionTreeClass(topNode);
        treeTop.child.push(topNode);
      } catch (error) {
        console.log('tree_generate_data_is_bug');
      }
    }; //nodeを再帰的に追加していく。
    //@param Nodeクラス node ノードの子要素を追加
    //@return Nodeクラス node 再起メソッドの返り値


    var parse = function parse(node, sepalete) {
      //nodeの子要素があるか、探索する
      sepalete.forEach(function (chain) {
        //chain変数のkeyに、親要素のidがある。
        //node.idが親要素のid
        if (node.id === Object.keys(chain)[0].split('.')[0]) {
          //@var Nodeクラス child 子ノードクラス。parseで子ノードの子要素を追加する
          var child = new Node(node.dir + '/' + Object.values(chain)[0].split('.')[1], Object.values(chain)[0].split('.')[0]);
          child.prototype = {
            chainparser: chainparser,
            tree: node.prototype.tree
          }; //子要素がある場合は、parseをもう一度呼び出す。
          //子要素の子要素を構築していく。

          node.child.push(parse(child, sepalete));
        }
      });
      return node;
    }; //@param Nodeクラス treeTop ツリー全体のノード
    //@param array projectionChain 投影データの配列
    //ノードを投影


    var syncProjection = function syncProjection(projectionChain) {
      //配列であるかをチェックする
      if (Array.isArray(projectionChain)) {
        projectionChain.forEach(function (chain) {
          //列挙可能か、判断する(このロジックで正しいか、わからない)
          if (chain !== undefined && chain !== null) {
            //@var Nodeクラス fromNode 投影元のノードクラス
            var fromNode = chainparser.searchNodeId(String(Object.keys(chain)[0]), treeTop); //@var Nodeクラス toNode 投影先のノードクラス

            var toNode = chainparser.searchNodeId(String(Object.values(chain)[0]), treeTop);

            if (fromNode !== undefined && toNode !== undefined) {
              //投影先と投影元をリンクさせる
              chainparser.syncLink(toNode, fromNode);
            }
          }
        });
      }
    }; //投影のノードを斜体にする
    //@param Nodeクラス treeTop ツリー全体のインスタンス


    var onSyncTree = function onSyncTree(treeTop) {
      //全ノードを取得して、回す
      chainparser.concatNode(treeTop).forEach(function (node) {
        //投影のノードを斜体にする
        if (typeof node.id === 'string') {
          if (node.id.substr(0, 2) === 'ta') {
            node.onSync();
          }
        }
      });
    }; //ツリーのdom要素を生成した時は、ツリーは表示しているので、非表示にする。
    //@param Nodeクラス treeTop ツリー全体のインスタンス


    var closeTree = function closeTree(treeTop) {
      treeTop.child.forEach(function (child) {
        if (child.className === 'expandtree' || child.className === 'lastexpandtree') {
          child.child.forEach(function (childChild) {
            childChild.noneDisplayTree();
          });
        }
      });
    }; //@var nodeクラス this.treeTop ツリーの一番上のノード


    var treeTop = new Node('', '1');

    if (document.getElementById('chaintree') && document.getElementById('chaintree').tagName === 'DIV') {
      //@var dom 仮のツリーのdom
      var fragment = document.createDocumentFragment(); //@var dom this.element dom要素を格納 ツリーのdom要素を加えていく一番上のツリー<div id="chaintree"></div>

      treeTop.element = document.getElementById('chaintree'); //@var string this.className ツリーのcssのクラス名

      treeTop.className = 'chaintree';

      if (Array.isArray(treesepalete)) {
        treesepalete.forEach(function (sepalete) {
          //treeTopのchildに、かたまりごとのノードを追加していく。
          createTopNode(sepalete, treeTop);
        });
      } //ツリーの全体のcssのクラス名を決める


      decisionClass(treeTop); //投影

      syncProjection(projectionChain); //domを生成して、ツリーを描画する

      createElement(treeTop, fragment); //投影を斜体にする

      onSyncTree(treeTop); //ツリーのdom要素を生成した時は、ツリーは表示しているので、非表示にする。

      closeTree(treeTop); //実際のdomにfragmentのdomを追加する

      treeTop.element.append(fragment);
    } else {
      console.log('chaintree div tag is no');
    } //ツリーインスタンスを返す


    return treeTop;
  }; //ツリーアクションクラス
  //@param array treesepalete 上下関係のオブジェクトのデータの配列
  //@param array projectionChain 投影データ
  //@return TreeActionクラス ツリー機能クラス


  TreeAction = function (treesepalete, projectionChain) {
    //@var Nodeクラス ノードクラス
    var Node = TreeAction.node; //@var ChainParser チェインパーサーのクラス

    var chainparser = TreeAction.chainparser; //@var Nodeクラス ツリーインスタンス

    var tree = TreeAction.createTree(treesepalete, projectionChain, Node, chainparser); //隠蔽/表示のメソッド
    //@param string nodeId 隠蔽するノードのid

    var changeDisplay = function changeDisplay(nodeId) {
      //nodeIdが文字列か判断する
      if (typeof nodeId === 'string') {
        //@var Nodeクラス 隠蔽/表示するノード
        var node = chainparser.searchNodeId(nodeId, tree); //nodeが見つかれば

        if (node !== undefined) {
          if (node.hide === false) {
            //displayがtrueの場合は、表示されているので、隠蔽する
            displayNoneNode(node);
          } else if (node.hide === true) {
            //displayがfalseならば、隠蔽しているので、表示する
            displayOpenNode(node);
          }
        }
      }
    }; //表示しているノードを隠蔽する。投影も含めて
    //@param Nodeクラス 隠蔽するノード


    var displayNoneNode = function displayNoneNode(node) {
      //隠蔽ノードのdisplayを変更する
      node.hide = true; //@var Nodeクラス 隠蔽するノードの親ノード

      var palent = chainparser.searchPalentNode(node.id, tree); //ノードクラスを隠蔽する

      chainparser.displayNone(node, palent); //隠蔽ノードに投影先があるなら、投影先も隠蔽する

      if (node.toLink !== []) {
        //投影先のidをループする
        node.toLink.forEach(function (linkNodeId) {
          //@var Nodeクラス 投影先のノード
          var linkNode = chainparser.searchNodeId(linkNodeId, tree); //displayを変更する

          linkNode.hide = true; //@var Nodeクラス 投影先の親ノード

          var linkPalent = chainparser.searchPalentNode(linkNodeId, tree); //投影先を隠蔽する

          chainparser.displayNone(linkNode, linkPalent);
        });
      }
    }; //隠蔽したノードを表示する。投影も含めて
    //@param Nodeクラス 表示するノード


    var displayOpenNode = function displayOpenNode(node) {
      //displayを変更する
      node.hide = false; //@var Nodeクラス 表示するノードの親ノード

      var palent = chainparser.searchPalentNode(node.id, tree); //ノードを表示する

      if (palent.element.children[0].children[0].innerText === '-') {
        chainparser.displayOpen(node, palent);
      } //表示ノードに投影先があるなら、投影先も表示する


      if (node.toLink !== []) {
        //投影先のidをループする
        node.toLink.forEach(function (linkNodeId) {
          //@var Nodeクラス 表示先のノード
          var linkNode = chainparser.searchNodeId(linkNodeId, tree); //displayを変更する

          linkNode.hide = false; //@var Nodeクラス 投影先の親ノード

          var linkPalent = chainparser.searchPalentNode(linkNodeId, tree); //表示されているツリー内であれば、表示する

          if (linkPalent.element.children[0].children[0].innerText === '-') {
            //投影先を表示する
            chainparser.displayOpen(linkNode, linkPalent);
          }
        });
      }
    }; //全体の隠蔽したツリーを表示する


    var openTree = function openTree() {
      //ツリー全体をループする
      chainparser.concatNode(tree).forEach(function (node) {
        if (node.hide === true) {
          //displayがfalseならば、隠蔽しているので、表示する
          //@var Nodeクラス 表示するノードの親ノード
          var palent = chainparser.searchPalentNode(node.id, tree);
          displayOpenNode(node, palent);
        }
      });
    }; //@var string nodeId 再表示するノードのid
    //閉じているノードを表示する


    var reOpenNode = function reOpenNode(nodeId) {
      //nodeIdを文字列か、調べる
      if (typeof nodeId === 'string') {
        //@var Nodeクラス 再表示するノード
        var node = chainparser.searchNodeId(nodeId, tree); //nodeが見つかれば

        if (node !== undefined) {
          //ツリーを下から上に開く
          node.openBottomUpTree(); //ノードをカレントにする

          node.focus(); //クリップボードのデータをカレントにする

          _ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.select(node.dir, node.id);
          _ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.current(node.dir, node.id);
        }
      }
    }; //現在のスパイラルでは使わない
    // let getLinetreeDir = function 
    //linetreeにあるか判断する
    //@var string nodeDir ノードクラスのディレクトリ
    //@return boolean linetreeにあるか判断する
    // let checkLineTree = function checkLineTree(nodeDir){
    //   //@var string topDir ディレクトリの第一階層のタイトル("部署")
    //   let topDir = getTopDir(nodeDir);
    //   //linetreeにあるか判断する
    //   if(topDir === "マイツリー" || topDir === "notitle"){
    //     return true;
    //   }else{
    //     return false;
    //   }
    // }
    //現在のスパイラルでは使わない
    //現在のスパイラルでは使わない
    //一番上位が同じか、判断する
    //@var string toNodeDir 先頭先のディレクトリ
    //@var string fromNodeDir コピーしたノードのディレクトリ
    //@return boolean 第一階層が同じか判断する
    // let checkSameTree = function checkSameTree(toNodeDir, fromNodeDir){
    //   //@var string toTopDir 第一階層のノードのタイトル
    //   let toTopDir = getTopDir(toNodeDir);
    //   //var sring fromToDir 第一階層のノードのタイトル
    //   let fromTopDir = getTopDir(fromNodeDir);
    //   //第一階層のタイトルが等しいなら、同じツリー内部にある
    //   if(toTopDir === fromTopDir){
    //     return true;
    //   }else{
    //     return false;
    //   }
    // }
    //現在のスパイラルでは使わない
    //現在のスパイラルでは使わない
    //第一階層のタイトルを取得する
    //@param string nodeDir ディレクトリ
    //@return string ディレクトリの第一階層のタイトル
    // let getTopDir = function getTopDir(nodeDir){
    //   //@var array splitToDir ディレクトリの文字を分割した配列
    //   let splitToDir = nodeDir.split('/');
    //   //先頭の空を削除する
    //   splitToDir.shift();
    //   //ディレクトリの一番先頭文字を取得する
    //   return splitToDir.shift();
    // }
    //現在のスパイラルでは使わない
    //ページ移動前のイベント


    window.addEventListener('beforeunload', function () {
      //@var array idを保存する連想配列
      var storage = {}; //@var array 隠蔽しいてるノードのidを保存する

      var hiddenStorage = {}; //全体のツリーを探索する

      chainparser.concatNode(tree).forEach(function (node) {
        //展開するツリーノードか、判断する
        if (node.className === 'expandtree' || node.className === 'lastexpandtree') {
          //展開している(非表示ではない)ノードのidを保存する
          if (!node.element.children[0].classList.value.match('unexpand')) {
            //ボックスがマイナス文字(開いているなら)
            if (node.element.children[0].children[0].innerText === '-') {
              //親のノードidを削除する
              //上からツリーを開くので、子ノードが開く時に、親ノードも開く
              Object.keys(storage).forEach(function (key) {
                //@var Nodeクラス 親ノード
                var palent = chainparser.searchPalentNode(node.id, tree); //storageに親ノードが保存してあれば、削除する

                if (palent.id === key) {
                  delete storage[key];
                }
              });
              storage[node.id] = node.id;
            }
          }
        } //displayがfalseなら、hiddenstorageに保存する


        if (node.hide === true) {
          hiddenStorage[node.id] = node.id;
        }
      }); //配列は保存できないので、json文字列に変換して保存する

      localStorage.setItem('id', JSON.stringify(storage));
      localStorage.setItem('hiddenId', JSON.stringify(hiddenStorage)); //クリップボードのデータを保存する

      storeClipboard();
    }); //ページ移動前のクリップボードのデータの保存

    var storeClipboard = function storeClipboard() {
      localStorage.setItem('currentId', _ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.getCurrentId());
      localStorage.setItem('currentDir', _ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.getCurrentDir());
      localStorage.setItem('selectId', _ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.getSelectId());
      localStorage.setItem('selectDir', _ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.getSelectDir());
    }; //ページ移動後のクリップボードのデータの復元


    var restoreClipboard = function restoreClipboard() {
      _ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.current(localStorage.getItem('currentDir'), localStorage.getItem('currentId'));
      _ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.select(localStorage.getItem('selectDir'), localStorage.getItem('selectId'));
    }; //カレントのデータをクリップボードのデータに代入する


    var currentClipboard = function currentClipboard(node) {
      _ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.current(node.dir, node.id);
      _ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.select(node.dir, node.id);
    }; //ページ移動後にページ移動前のノードを開く


    var openNodeAfterPageMove = function openNodeAfterPageMove() {
      //@var array ツリーを開いていたノードのidの配列
      var storage = JSON.parse(localStorage.getItem('id')); //ページ移動前に開いていたノードを開く

      if (storage !== null && storage !== undefined) {
        Object.keys(storage).forEach(function (id) {
          //@var Nodeクラス 開くノード
          var node = chainparser.searchNodeId(String(storage[id]), tree);

          if (node !== undefined) {
            node.openBottomUpTreePageMove();
          }
        });
      }
    }; //ページ移動前に、隠蔽していたノードを、ページ移動後にも隠蔽する


    var hideDisplayAfterPageMove = function hideDisplayAfterPageMove() {
      //@var array 隠蔽していたノードのid
      var hiddenStorage = JSON.parse(localStorage.getItem('hiddenId'));

      if (hiddenStorage !== null && hiddenStorage !== undefined) {
        Object.keys(hiddenStorage).forEach(function (id) {
          //@var Nodeクラス 隠蔽するノード
          var node = chainparser.searchNodeId(hiddenStorage[id], tree);

          if (node !== undefined) {
            displayNoneNode(node);
          }
        });
      }
    }; //ノードまでスクロールする
    //@var Nodeクラス スクロールするノード


    var scrollNode = function scrollNode(node) {
      //@var dom id=treeのdom
      var treeTag = document.getElementById('tree'); //@var int ツリー画面の表示領域とノードの位置の差(width)

      var left = (treeTag.getBoundingClientRect().left + treeTag.clientWidth) * 0.6 - node.element.getBoundingClientRect().left; //@var int ツリー画面の表示領域とノードの位置の差(height)

      var top = (treeTag.getBoundingClientRect().top + treeTag.clientHeight) * 0.6 - node.element.getBoundingClientRect().top; //表示領域よりはみ出していたら、移動する

      if (left < 0) {
        treeTag.scrollLeft = 18 - left + 75;
      }

      if (top < 0) {
        treeTag.scrollTop = 9 - top + 60;
      }
    }; //ページ移動前のクリップボードのデータを復元する


    restoreClipboard(); //ページ移動前に開いていたノードを開く

    openNodeAfterPageMove(); //ページ移動前に隠蔽していたノードを閉じる

    hideDisplayAfterPageMove(); //一覧表示からのノードの表示

    if (document.location.pathname.split('/')[1] === 'show') {
      //@var Nodeクラス 開くノード
      var node; //@var array パスをスラッシュで区切った配列

      var pathArray = document.getElementById('copyTarget').parentNode.action.split('/'); //@var string 詳細行の部署のid

      var nodeId = pathArray[pathArray.length - 1]; //投影のノードをクリックしたら

      if (nodeId.slice(0, 2) === 'ta') {
        //@var Nodeクラス 投影ノード
        var projectionNode = chainparser.searchNodeId(nodeId, tree); //投影元のノードを取得する

        node = chainparser.searchNodeId(projectionNode.fromLink[0], tree);
      } else {
        //@var Nodeクラス 開くノード
        node = chainparser.searchNodeId(nodeId, tree);
      }

      if (node !== undefined) {
        //ノードを開く
        node.openBottomUpTree(); //ノードをカレントにする

        node.focus();
        scrollNode(node); //クリップボードのデータをカレントにする

        currentClipboard(node);
      }
    } else if (document.location.pathname.split('/')[1] === '') {
      //indexルートの場合
      //@var Nodeクラス カレントにするノード 
      var _node = chainparser.searchNodeId('bs00000001', tree);

      if (_node !== undefined) {
        _node.openBottomUpTree();

        _node.focus();

        scrollNode(_node);
        currentClipboard(_node);
      }
    } else {
      //複写、移動、削除の場合
      if (document.getElementById('back_treeaction').value === 'delete' || document.getElementById('back_treeaction').value === 'open') {
        //@var Nodeクラス ツリーの開くノード
        var _node2 = chainparser.searchNodeId(document.getElementById('action_node_id').value, tree);

        if (_node2 !== undefined) {
          //ノードを開く
          _node2.openBottomUpTree(); //ノードをカレントにする


          _node2.focus();

          scrollNode(_node2); //クリップボードのデータをカレントにする

          currentClipboard(_node2);
        }
      } else {
        //showルーティングでも、ツリー機能ルーティングでもない場合
        //@var Nodeクラス カレントにするノード 
        var _node3 = chainparser.searchNodeId(_ptcmcb__WEBPACK_IMPORTED_MODULE_0__.clipboard.getCurrentId(), tree);

        if (_node3 !== undefined) {
          _node3.openBottomUpTree();

          _node3.focus();

          scrollNode(_node3);
          currentClipboard(_node3);
        }
      }
    }

    return {
      openTree: openTree,
      changeDisplay: changeDisplay,
      reOpenNode: reOpenNode
    };
  }(treeChain, projectionChain); //隠蔽のイベント
  //詳細行の表示ではない時は、イベントを追加しない


  if (document.getElementById('tree_change_display')) {
    document.getElementById('tree_change_display').addEventListener('click', function () {
      //@var array パスをスラッシュで区切った配列
      var pathArray = document.getElementById('copyTarget').parentNode.action.split('/'); //@var string 詳細行のid

      var nodeId = pathArray[pathArray.length - 1]; //隠蔽のメソッド

      TreeAction.changeDisplay(nodeId);
    });
  } //再表示イベント
  //詳細行の表示ではない時は、イベントを追加しない


  if (document.getElementById('open_tree')) {
    document.getElementById('open_tree').addEventListener('click', function () {
      //@var array パスをスラッシュで区切った配列
      var pathArray = document.getElementById('copyTarget').parentNode.action.split('/'); //@var string 詳細行のid

      var nodeId = pathArray[pathArray.length - 1]; //隠蔽のメソッド

      TreeAction.reOpenNode(nodeId);
    });
  } //ツリー画面の露出ボタンのクリックイベント


  document.getElementById('openTree').addEventListener('click', function () {
    TreeAction.openTree();
  });
} else {
  document.getElementById('tree').style = 'display: none';
}



/***/ }),

/***/ "./resources/js/work_js/ptcmtp.js":
/*!****************************************!*\
  !*** ./resources/js/work_js/ptcmtp.js ***!
  \****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "customToolTip": () => (/* binding */ customToolTip)
/* harmony export */ });
/* harmony import */ var _ptcmrd__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ptcmrd */ "./resources/js/work_js/ptcmrd.js");
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _wrapNativeSuper(Class) { var _cache = typeof Map === "function" ? new Map() : undefined; _wrapNativeSuper = function _wrapNativeSuper(Class) { if (Class === null || !_isNativeFunction(Class)) return Class; if (typeof Class !== "function") { throw new TypeError("Super expression must either be null or a function"); } if (typeof _cache !== "undefined") { if (_cache.has(Class)) return _cache.get(Class); _cache.set(Class, Wrapper); } function Wrapper() { return _construct(Class, arguments, _getPrototypeOf(this).constructor); } Wrapper.prototype = Object.create(Class.prototype, { constructor: { value: Wrapper, enumerable: false, writable: true, configurable: true } }); return _setPrototypeOf(Wrapper, Class); }; return _wrapNativeSuper(Class); }

function _construct(Parent, args, Class) { if (_isNativeReflectConstruct()) { _construct = Reflect.construct; } else { _construct = function _construct(Parent, args, Class) { var a = [null]; a.push.apply(a, args); var Constructor = Function.bind.apply(Parent, a); var instance = new Constructor(); if (Class) _setPrototypeOf(instance, Class.prototype); return instance; }; } return _construct.apply(null, arguments); }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }

function _isNativeFunction(fn) { return Function.toString.call(fn).indexOf("[native code]") !== -1; }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }

 //@var object ツールチップのクラス

var customToolTip = {};

if (_ptcmrd__WEBPACK_IMPORTED_MODULE_0__.findMobile.deviceName !== 'pc') {
  customToolTip = function () {
    //<custom-tooltip></custom-tooltip>の宣言
    var CustomToolTip = /*#__PURE__*/function (_HTMLElement) {
      _inherits(CustomToolTip, _HTMLElement);

      var _super = _createSuper(CustomToolTip);

      //ツールチップのdom生成
      function CustomToolTip() {
        var _this;

        _classCallCheck(this, CustomToolTip);

        _this = _super.call(this);

        _this.updateRendering();

        return _this;
      } //domの生成と描画


      _createClass(CustomToolTip, [{
        key: "updateRendering",
        value: function updateRendering() {
          //@var dom ？マーク
          var span = this.createSelect(); //custom-tooltipタグに追加する

          this.appendChild(span); //ツールチップの表示の基準とする

          this.style.position = 'relative'; //@var dom ツールチップのdom

          var tooltipSpan = this.createTooltip();
          tooltipSpan = this.appendChild(tooltipSpan); //ツールチップの表示が画面よりはみ出ていたら、修正する

          this.changePosition(span, tooltipSpan); //？マークのクリック処理

          span.addEventListener('click', function () {
            //非表示なら表示
            if (tooltipSpan.style.visibility === 'hidden') {
              tooltipSpan.style.visibility = 'visible';
            }
          }); //表示したツールチップを非表示にする

          function clearToolTip() {
            if (this.tooltipSpan.style.visibility === 'visible') {
              this.tooltipSpan.style.visibility = 'hidden';
            }
          } //？マーク以外をクリックしたら、ツールチップを非表示にする


          window.addEventListener('click', {
            tooltipSpan: tooltipSpan,
            handleEvent: clearToolTip
          }, true);
        } //？マークの生成
        //@return dom ？マーク

      }, {
        key: "createSelect",
        value: function createSelect() {
          //@var dom ？マーク
          var span = document.createElement('span');
          span.innerText = '?';
          span.style.color = 'red';
          span.style.borderBottom = '1px solid blue';
          span.style.margin = '2px';
          return span;
        } //ツールチップのdomの生成
        //@var dom ツールチップのdom

      }, {
        key: "createTooltip",
        value: function createTooltip() {
          //@var dom ツールチップのdom
          var tooltipSpan = document.createElement('span');
          tooltipSpan.style.position = 'absolute';
          tooltipSpan.style.width = '10rem';
          tooltipSpan.style.top = '1rem';
          tooltipSpan.style.left = '1rem'; //改行を実現する

          tooltipSpan.style.whiteSpace = 'pre-line !important';
          tooltipSpan.style.backgroundColor = 'white';
          tooltipSpan.style.visibility = 'hidden';
          tooltipSpan.style.zIndex = '500';
          tooltipSpan.style.border = '1px solid';
          tooltipSpan.style.fontWeight = 'bold';
          tooltipSpan.innerText = this.getAttribute('title');

          if (this.getAttribute('title').length >= 100) {
            tooltipSpan.style.width = '20rem';
          }

          return tooltipSpan;
        } //ツールチップが画面からはみ出していたら、位置を変更する
        //@param dom span ？マーク
        //@param dom tooltip_span ツールチップのdom
        //@return dom ツールチップのdom

      }, {
        key: "changePosition",
        value: function changePosition(span, tooltipSpan) {
          //@var int spanの表示位置
          var px = window.pageXOffset + span.getBoundingClientRect().left; //@vat int 画面の長さ と spanとツールチップの長さの差

          var diffWidth = document.body.clientWidth - (px + tooltipSpan.clientWidth); //横にはみ出していたら、横にずらす

          if (diffWidth < 0) {
            tooltipSpan.style.left = diffWidth - 8 + 'px';
          } //@var int spanの表示位置


          var py = window.pageYOffset + span.getBoundingClientRect().top; //@var int 画面の高さ と spanとツールチップの高さの差

          var diffHight = document.body.clientHeight - (py + tooltipSpan.clientHeight); //縦にはみ出していたら、下げる

          if (diffHight < 0) {
            tooltipSpan.style.top = -tooltipSpan.clientHeight - 8 + 'px';
          }

          return tooltipSpan;
        }
      }]);

      return CustomToolTip;
    }( /*#__PURE__*/_wrapNativeSuper(HTMLElement)); //custom-tooltipを使えるように設定する


    customElements.define('custom-tooltip', CustomToolTip);
  }();
}



/***/ }),

/***/ "./node_modules/lodash/lodash.js":
/*!***************************************!*\
  !*** ./node_modules/lodash/lodash.js ***!
  \***************************************/
/***/ (function(module, exports, __webpack_require__) {

/* module decorator */ module = __webpack_require__.nmd(module);
var __WEBPACK_AMD_DEFINE_RESULT__;/**
 * @license
 * Lodash <https://lodash.com/>
 * Copyright OpenJS Foundation and other contributors <https://openjsf.org/>
 * Released under MIT license <https://lodash.com/license>
 * Based on Underscore.js 1.8.3 <http://underscorejs.org/LICENSE>
 * Copyright Jeremy Ashkenas, DocumentCloud and Investigative Reporters & Editors
 */
;(function() {

  /** Used as a safe reference for `undefined` in pre-ES5 environments. */
  var undefined;

  /** Used as the semantic version number. */
  var VERSION = '4.17.21';

  /** Used as the size to enable large array optimizations. */
  var LARGE_ARRAY_SIZE = 200;

  /** Error message constants. */
  var CORE_ERROR_TEXT = 'Unsupported core-js use. Try https://npms.io/search?q=ponyfill.',
      FUNC_ERROR_TEXT = 'Expected a function',
      INVALID_TEMPL_VAR_ERROR_TEXT = 'Invalid `variable` option passed into `_.template`';

  /** Used to stand-in for `undefined` hash values. */
  var HASH_UNDEFINED = '__lodash_hash_undefined__';

  /** Used as the maximum memoize cache size. */
  var MAX_MEMOIZE_SIZE = 500;

  /** Used as the internal argument placeholder. */
  var PLACEHOLDER = '__lodash_placeholder__';

  /** Used to compose bitmasks for cloning. */
  var CLONE_DEEP_FLAG = 1,
      CLONE_FLAT_FLAG = 2,
      CLONE_SYMBOLS_FLAG = 4;

  /** Used to compose bitmasks for value comparisons. */
  var COMPARE_PARTIAL_FLAG = 1,
      COMPARE_UNORDERED_FLAG = 2;

  /** Used to compose bitmasks for function metadata. */
  var WRAP_BIND_FLAG = 1,
      WRAP_BIND_KEY_FLAG = 2,
      WRAP_CURRY_BOUND_FLAG = 4,
      WRAP_CURRY_FLAG = 8,
      WRAP_CURRY_RIGHT_FLAG = 16,
      WRAP_PARTIAL_FLAG = 32,
      WRAP_PARTIAL_RIGHT_FLAG = 64,
      WRAP_ARY_FLAG = 128,
      WRAP_REARG_FLAG = 256,
      WRAP_FLIP_FLAG = 512;

  /** Used as default options for `_.truncate`. */
  var DEFAULT_TRUNC_LENGTH = 30,
      DEFAULT_TRUNC_OMISSION = '...';

  /** Used to detect hot functions by number of calls within a span of milliseconds. */
  var HOT_COUNT = 800,
      HOT_SPAN = 16;

  /** Used to indicate the type of lazy iteratees. */
  var LAZY_FILTER_FLAG = 1,
      LAZY_MAP_FLAG = 2,
      LAZY_WHILE_FLAG = 3;

  /** Used as references for various `Number` constants. */
  var INFINITY = 1 / 0,
      MAX_SAFE_INTEGER = 9007199254740991,
      MAX_INTEGER = 1.7976931348623157e+308,
      NAN = 0 / 0;

  /** Used as references for the maximum length and index of an array. */
  var MAX_ARRAY_LENGTH = 4294967295,
      MAX_ARRAY_INDEX = MAX_ARRAY_LENGTH - 1,
      HALF_MAX_ARRAY_LENGTH = MAX_ARRAY_LENGTH >>> 1;

  /** Used to associate wrap methods with their bit flags. */
  var wrapFlags = [
    ['ary', WRAP_ARY_FLAG],
    ['bind', WRAP_BIND_FLAG],
    ['bindKey', WRAP_BIND_KEY_FLAG],
    ['curry', WRAP_CURRY_FLAG],
    ['curryRight', WRAP_CURRY_RIGHT_FLAG],
    ['flip', WRAP_FLIP_FLAG],
    ['partial', WRAP_PARTIAL_FLAG],
    ['partialRight', WRAP_PARTIAL_RIGHT_FLAG],
    ['rearg', WRAP_REARG_FLAG]
  ];

  /** `Object#toString` result references. */
  var argsTag = '[object Arguments]',
      arrayTag = '[object Array]',
      asyncTag = '[object AsyncFunction]',
      boolTag = '[object Boolean]',
      dateTag = '[object Date]',
      domExcTag = '[object DOMException]',
      errorTag = '[object Error]',
      funcTag = '[object Function]',
      genTag = '[object GeneratorFunction]',
      mapTag = '[object Map]',
      numberTag = '[object Number]',
      nullTag = '[object Null]',
      objectTag = '[object Object]',
      promiseTag = '[object Promise]',
      proxyTag = '[object Proxy]',
      regexpTag = '[object RegExp]',
      setTag = '[object Set]',
      stringTag = '[object String]',
      symbolTag = '[object Symbol]',
      undefinedTag = '[object Undefined]',
      weakMapTag = '[object WeakMap]',
      weakSetTag = '[object WeakSet]';

  var arrayBufferTag = '[object ArrayBuffer]',
      dataViewTag = '[object DataView]',
      float32Tag = '[object Float32Array]',
      float64Tag = '[object Float64Array]',
      int8Tag = '[object Int8Array]',
      int16Tag = '[object Int16Array]',
      int32Tag = '[object Int32Array]',
      uint8Tag = '[object Uint8Array]',
      uint8ClampedTag = '[object Uint8ClampedArray]',
      uint16Tag = '[object Uint16Array]',
      uint32Tag = '[object Uint32Array]';

  /** Used to match empty string literals in compiled template source. */
  var reEmptyStringLeading = /\b__p \+= '';/g,
      reEmptyStringMiddle = /\b(__p \+=) '' \+/g,
      reEmptyStringTrailing = /(__e\(.*?\)|\b__t\)) \+\n'';/g;

  /** Used to match HTML entities and HTML characters. */
  var reEscapedHtml = /&(?:amp|lt|gt|quot|#39);/g,
      reUnescapedHtml = /[&<>"']/g,
      reHasEscapedHtml = RegExp(reEscapedHtml.source),
      reHasUnescapedHtml = RegExp(reUnescapedHtml.source);

  /** Used to match template delimiters. */
  var reEscape = /<%-([\s\S]+?)%>/g,
      reEvaluate = /<%([\s\S]+?)%>/g,
      reInterpolate = /<%=([\s\S]+?)%>/g;

  /** Used to match property names within property paths. */
  var reIsDeepProp = /\.|\[(?:[^[\]]*|(["'])(?:(?!\1)[^\\]|\\.)*?\1)\]/,
      reIsPlainProp = /^\w*$/,
      rePropName = /[^.[\]]+|\[(?:(-?\d+(?:\.\d+)?)|(["'])((?:(?!\2)[^\\]|\\.)*?)\2)\]|(?=(?:\.|\[\])(?:\.|\[\]|$))/g;

  /**
   * Used to match `RegExp`
   * [syntax characters](http://ecma-international.org/ecma-262/7.0/#sec-patterns).
   */
  var reRegExpChar = /[\\^$.*+?()[\]{}|]/g,
      reHasRegExpChar = RegExp(reRegExpChar.source);

  /** Used to match leading whitespace. */
  var reTrimStart = /^\s+/;

  /** Used to match a single whitespace character. */
  var reWhitespace = /\s/;

  /** Used to match wrap detail comments. */
  var reWrapComment = /\{(?:\n\/\* \[wrapped with .+\] \*\/)?\n?/,
      reWrapDetails = /\{\n\/\* \[wrapped with (.+)\] \*/,
      reSplitDetails = /,? & /;

  /** Used to match words composed of alphanumeric characters. */
  var reAsciiWord = /[^\x00-\x2f\x3a-\x40\x5b-\x60\x7b-\x7f]+/g;

  /**
   * Used to validate the `validate` option in `_.template` variable.
   *
   * Forbids characters which could potentially change the meaning of the function argument definition:
   * - "()," (modification of function parameters)
   * - "=" (default value)
   * - "[]{}" (destructuring of function parameters)
   * - "/" (beginning of a comment)
   * - whitespace
   */
  var reForbiddenIdentifierChars = /[()=,{}\[\]\/\s]/;

  /** Used to match backslashes in property paths. */
  var reEscapeChar = /\\(\\)?/g;

  /**
   * Used to match
   * [ES template delimiters](http://ecma-international.org/ecma-262/7.0/#sec-template-literal-lexical-components).
   */
  var reEsTemplate = /\$\{([^\\}]*(?:\\.[^\\}]*)*)\}/g;

  /** Used to match `RegExp` flags from their coerced string values. */
  var reFlags = /\w*$/;

  /** Used to detect bad signed hexadecimal string values. */
  var reIsBadHex = /^[-+]0x[0-9a-f]+$/i;

  /** Used to detect binary string values. */
  var reIsBinary = /^0b[01]+$/i;

  /** Used to detect host constructors (Safari). */
  var reIsHostCtor = /^\[object .+?Constructor\]$/;

  /** Used to detect octal string values. */
  var reIsOctal = /^0o[0-7]+$/i;

  /** Used to detect unsigned integer values. */
  var reIsUint = /^(?:0|[1-9]\d*)$/;

  /** Used to match Latin Unicode letters (excluding mathematical operators). */
  var reLatin = /[\xc0-\xd6\xd8-\xf6\xf8-\xff\u0100-\u017f]/g;

  /** Used to ensure capturing order of template delimiters. */
  var reNoMatch = /($^)/;

  /** Used to match unescaped characters in compiled string literals. */
  var reUnescapedString = /['\n\r\u2028\u2029\\]/g;

  /** Used to compose unicode character classes. */
  var rsAstralRange = '\\ud800-\\udfff',
      rsComboMarksRange = '\\u0300-\\u036f',
      reComboHalfMarksRange = '\\ufe20-\\ufe2f',
      rsComboSymbolsRange = '\\u20d0-\\u20ff',
      rsComboRange = rsComboMarksRange + reComboHalfMarksRange + rsComboSymbolsRange,
      rsDingbatRange = '\\u2700-\\u27bf',
      rsLowerRange = 'a-z\\xdf-\\xf6\\xf8-\\xff',
      rsMathOpRange = '\\xac\\xb1\\xd7\\xf7',
      rsNonCharRange = '\\x00-\\x2f\\x3a-\\x40\\x5b-\\x60\\x7b-\\xbf',
      rsPunctuationRange = '\\u2000-\\u206f',
      rsSpaceRange = ' \\t\\x0b\\f\\xa0\\ufeff\\n\\r\\u2028\\u2029\\u1680\\u180e\\u2000\\u2001\\u2002\\u2003\\u2004\\u2005\\u2006\\u2007\\u2008\\u2009\\u200a\\u202f\\u205f\\u3000',
      rsUpperRange = 'A-Z\\xc0-\\xd6\\xd8-\\xde',
      rsVarRange = '\\ufe0e\\ufe0f',
      rsBreakRange = rsMathOpRange + rsNonCharRange + rsPunctuationRange + rsSpaceRange;

  /** Used to compose unicode capture groups. */
  var rsApos = "['\u2019]",
      rsAstral = '[' + rsAstralRange + ']',
      rsBreak = '[' + rsBreakRange + ']',
      rsCombo = '[' + rsComboRange + ']',
      rsDigits = '\\d+',
      rsDingbat = '[' + rsDingbatRange + ']',
      rsLower = '[' + rsLowerRange + ']',
      rsMisc = '[^' + rsAstralRange + rsBreakRange + rsDigits + rsDingbatRange + rsLowerRange + rsUpperRange + ']',
      rsFitz = '\\ud83c[\\udffb-\\udfff]',
      rsModifier = '(?:' + rsCombo + '|' + rsFitz + ')',
      rsNonAstral = '[^' + rsAstralRange + ']',
      rsRegional = '(?:\\ud83c[\\udde6-\\uddff]){2}',
      rsSurrPair = '[\\ud800-\\udbff][\\udc00-\\udfff]',
      rsUpper = '[' + rsUpperRange + ']',
      rsZWJ = '\\u200d';

  /** Used to compose unicode regexes. */
  var rsMiscLower = '(?:' + rsLower + '|' + rsMisc + ')',
      rsMiscUpper = '(?:' + rsUpper + '|' + rsMisc + ')',
      rsOptContrLower = '(?:' + rsApos + '(?:d|ll|m|re|s|t|ve))?',
      rsOptContrUpper = '(?:' + rsApos + '(?:D|LL|M|RE|S|T|VE))?',
      reOptMod = rsModifier + '?',
      rsOptVar = '[' + rsVarRange + ']?',
      rsOptJoin = '(?:' + rsZWJ + '(?:' + [rsNonAstral, rsRegional, rsSurrPair].join('|') + ')' + rsOptVar + reOptMod + ')*',
      rsOrdLower = '\\d*(?:1st|2nd|3rd|(?![123])\\dth)(?=\\b|[A-Z_])',
      rsOrdUpper = '\\d*(?:1ST|2ND|3RD|(?![123])\\dTH)(?=\\b|[a-z_])',
      rsSeq = rsOptVar + reOptMod + rsOptJoin,
      rsEmoji = '(?:' + [rsDingbat, rsRegional, rsSurrPair].join('|') + ')' + rsSeq,
      rsSymbol = '(?:' + [rsNonAstral + rsCombo + '?', rsCombo, rsRegional, rsSurrPair, rsAstral].join('|') + ')';

  /** Used to match apostrophes. */
  var reApos = RegExp(rsApos, 'g');

  /**
   * Used to match [combining diacritical marks](https://en.wikipedia.org/wiki/Combining_Diacritical_Marks) and
   * [combining diacritical marks for symbols](https://en.wikipedia.org/wiki/Combining_Diacritical_Marks_for_Symbols).
   */
  var reComboMark = RegExp(rsCombo, 'g');

  /** Used to match [string symbols](https://mathiasbynens.be/notes/javascript-unicode). */
  var reUnicode = RegExp(rsFitz + '(?=' + rsFitz + ')|' + rsSymbol + rsSeq, 'g');

  /** Used to match complex or compound words. */
  var reUnicodeWord = RegExp([
    rsUpper + '?' + rsLower + '+' + rsOptContrLower + '(?=' + [rsBreak, rsUpper, '$'].join('|') + ')',
    rsMiscUpper + '+' + rsOptContrUpper + '(?=' + [rsBreak, rsUpper + rsMiscLower, '$'].join('|') + ')',
    rsUpper + '?' + rsMiscLower + '+' + rsOptContrLower,
    rsUpper + '+' + rsOptContrUpper,
    rsOrdUpper,
    rsOrdLower,
    rsDigits,
    rsEmoji
  ].join('|'), 'g');

  /** Used to detect strings with [zero-width joiners or code points from the astral planes](http://eev.ee/blog/2015/09/12/dark-corners-of-unicode/). */
  var reHasUnicode = RegExp('[' + rsZWJ + rsAstralRange  + rsComboRange + rsVarRange + ']');

  /** Used to detect strings that need a more robust regexp to match words. */
  var reHasUnicodeWord = /[a-z][A-Z]|[A-Z]{2}[a-z]|[0-9][a-zA-Z]|[a-zA-Z][0-9]|[^a-zA-Z0-9 ]/;

  /** Used to assign default `context` object properties. */
  var contextProps = [
    'Array', 'Buffer', 'DataView', 'Date', 'Error', 'Float32Array', 'Float64Array',
    'Function', 'Int8Array', 'Int16Array', 'Int32Array', 'Map', 'Math', 'Object',
    'Promise', 'RegExp', 'Set', 'String', 'Symbol', 'TypeError', 'Uint8Array',
    'Uint8ClampedArray', 'Uint16Array', 'Uint32Array', 'WeakMap',
    '_', 'clearTimeout', 'isFinite', 'parseInt', 'setTimeout'
  ];

  /** Used to make template sourceURLs easier to identify. */
  var templateCounter = -1;

  /** Used to identify `toStringTag` values of typed arrays. */
  var typedArrayTags = {};
  typedArrayTags[float32Tag] = typedArrayTags[float64Tag] =
  typedArrayTags[int8Tag] = typedArrayTags[int16Tag] =
  typedArrayTags[int32Tag] = typedArrayTags[uint8Tag] =
  typedArrayTags[uint8ClampedTag] = typedArrayTags[uint16Tag] =
  typedArrayTags[uint32Tag] = true;
  typedArrayTags[argsTag] = typedArrayTags[arrayTag] =
  typedArrayTags[arrayBufferTag] = typedArrayTags[boolTag] =
  typedArrayTags[dataViewTag] = typedArrayTags[dateTag] =
  typedArrayTags[errorTag] = typedArrayTags[funcTag] =
  typedArrayTags[mapTag] = typedArrayTags[numberTag] =
  typedArrayTags[objectTag] = typedArrayTags[regexpTag] =
  typedArrayTags[setTag] = typedArrayTags[stringTag] =
  typedArrayTags[weakMapTag] = false;

  /** Used to identify `toStringTag` values supported by `_.clone`. */
  var cloneableTags = {};
  cloneableTags[argsTag] = cloneableTags[arrayTag] =
  cloneableTags[arrayBufferTag] = cloneableTags[dataViewTag] =
  cloneableTags[boolTag] = cloneableTags[dateTag] =
  cloneableTags[float32Tag] = cloneableTags[float64Tag] =
  cloneableTags[int8Tag] = cloneableTags[int16Tag] =
  cloneableTags[int32Tag] = cloneableTags[mapTag] =
  cloneableTags[numberTag] = cloneableTags[objectTag] =
  cloneableTags[regexpTag] = cloneableTags[setTag] =
  cloneableTags[stringTag] = cloneableTags[symbolTag] =
  cloneableTags[uint8Tag] = cloneableTags[uint8ClampedTag] =
  cloneableTags[uint16Tag] = cloneableTags[uint32Tag] = true;
  cloneableTags[errorTag] = cloneableTags[funcTag] =
  cloneableTags[weakMapTag] = false;

  /** Used to map Latin Unicode letters to basic Latin letters. */
  var deburredLetters = {
    // Latin-1 Supplement block.
    '\xc0': 'A',  '\xc1': 'A', '\xc2': 'A', '\xc3': 'A', '\xc4': 'A', '\xc5': 'A',
    '\xe0': 'a',  '\xe1': 'a', '\xe2': 'a', '\xe3': 'a', '\xe4': 'a', '\xe5': 'a',
    '\xc7': 'C',  '\xe7': 'c',
    '\xd0': 'D',  '\xf0': 'd',
    '\xc8': 'E',  '\xc9': 'E', '\xca': 'E', '\xcb': 'E',
    '\xe8': 'e',  '\xe9': 'e', '\xea': 'e', '\xeb': 'e',
    '\xcc': 'I',  '\xcd': 'I', '\xce': 'I', '\xcf': 'I',
    '\xec': 'i',  '\xed': 'i', '\xee': 'i', '\xef': 'i',
    '\xd1': 'N',  '\xf1': 'n',
    '\xd2': 'O',  '\xd3': 'O', '\xd4': 'O', '\xd5': 'O', '\xd6': 'O', '\xd8': 'O',
    '\xf2': 'o',  '\xf3': 'o', '\xf4': 'o', '\xf5': 'o', '\xf6': 'o', '\xf8': 'o',
    '\xd9': 'U',  '\xda': 'U', '\xdb': 'U', '\xdc': 'U',
    '\xf9': 'u',  '\xfa': 'u', '\xfb': 'u', '\xfc': 'u',
    '\xdd': 'Y',  '\xfd': 'y', '\xff': 'y',
    '\xc6': 'Ae', '\xe6': 'ae',
    '\xde': 'Th', '\xfe': 'th',
    '\xdf': 'ss',
    // Latin Extended-A block.
    '\u0100': 'A',  '\u0102': 'A', '\u0104': 'A',
    '\u0101': 'a',  '\u0103': 'a', '\u0105': 'a',
    '\u0106': 'C',  '\u0108': 'C', '\u010a': 'C', '\u010c': 'C',
    '\u0107': 'c',  '\u0109': 'c', '\u010b': 'c', '\u010d': 'c',
    '\u010e': 'D',  '\u0110': 'D', '\u010f': 'd', '\u0111': 'd',
    '\u0112': 'E',  '\u0114': 'E', '\u0116': 'E', '\u0118': 'E', '\u011a': 'E',
    '\u0113': 'e',  '\u0115': 'e', '\u0117': 'e', '\u0119': 'e', '\u011b': 'e',
    '\u011c': 'G',  '\u011e': 'G', '\u0120': 'G', '\u0122': 'G',
    '\u011d': 'g',  '\u011f': 'g', '\u0121': 'g', '\u0123': 'g',
    '\u0124': 'H',  '\u0126': 'H', '\u0125': 'h', '\u0127': 'h',
    '\u0128': 'I',  '\u012a': 'I', '\u012c': 'I', '\u012e': 'I', '\u0130': 'I',
    '\u0129': 'i',  '\u012b': 'i', '\u012d': 'i', '\u012f': 'i', '\u0131': 'i',
    '\u0134': 'J',  '\u0135': 'j',
    '\u0136': 'K',  '\u0137': 'k', '\u0138': 'k',
    '\u0139': 'L',  '\u013b': 'L', '\u013d': 'L', '\u013f': 'L', '\u0141': 'L',
    '\u013a': 'l',  '\u013c': 'l', '\u013e': 'l', '\u0140': 'l', '\u0142': 'l',
    '\u0143': 'N',  '\u0145': 'N', '\u0147': 'N', '\u014a': 'N',
    '\u0144': 'n',  '\u0146': 'n', '\u0148': 'n', '\u014b': 'n',
    '\u014c': 'O',  '\u014e': 'O', '\u0150': 'O',
    '\u014d': 'o',  '\u014f': 'o', '\u0151': 'o',
    '\u0154': 'R',  '\u0156': 'R', '\u0158': 'R',
    '\u0155': 'r',  '\u0157': 'r', '\u0159': 'r',
    '\u015a': 'S',  '\u015c': 'S', '\u015e': 'S', '\u0160': 'S',
    '\u015b': 's',  '\u015d': 's', '\u015f': 's', '\u0161': 's',
    '\u0162': 'T',  '\u0164': 'T', '\u0166': 'T',
    '\u0163': 't',  '\u0165': 't', '\u0167': 't',
    '\u0168': 'U',  '\u016a': 'U', '\u016c': 'U', '\u016e': 'U', '\u0170': 'U', '\u0172': 'U',
    '\u0169': 'u',  '\u016b': 'u', '\u016d': 'u', '\u016f': 'u', '\u0171': 'u', '\u0173': 'u',
    '\u0174': 'W',  '\u0175': 'w',
    '\u0176': 'Y',  '\u0177': 'y', '\u0178': 'Y',
    '\u0179': 'Z',  '\u017b': 'Z', '\u017d': 'Z',
    '\u017a': 'z',  '\u017c': 'z', '\u017e': 'z',
    '\u0132': 'IJ', '\u0133': 'ij',
    '\u0152': 'Oe', '\u0153': 'oe',
    '\u0149': "'n", '\u017f': 's'
  };

  /** Used to map characters to HTML entities. */
  var htmlEscapes = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;'
  };

  /** Used to map HTML entities to characters. */
  var htmlUnescapes = {
    '&amp;': '&',
    '&lt;': '<',
    '&gt;': '>',
    '&quot;': '"',
    '&#39;': "'"
  };

  /** Used to escape characters for inclusion in compiled string literals. */
  var stringEscapes = {
    '\\': '\\',
    "'": "'",
    '\n': 'n',
    '\r': 'r',
    '\u2028': 'u2028',
    '\u2029': 'u2029'
  };

  /** Built-in method references without a dependency on `root`. */
  var freeParseFloat = parseFloat,
      freeParseInt = parseInt;

  /** Detect free variable `global` from Node.js. */
  var freeGlobal = typeof __webpack_require__.g == 'object' && __webpack_require__.g && __webpack_require__.g.Object === Object && __webpack_require__.g;

  /** Detect free variable `self`. */
  var freeSelf = typeof self == 'object' && self && self.Object === Object && self;

  /** Used as a reference to the global object. */
  var root = freeGlobal || freeSelf || Function('return this')();

  /** Detect free variable `exports`. */
  var freeExports =  true && exports && !exports.nodeType && exports;

  /** Detect free variable `module`. */
  var freeModule = freeExports && "object" == 'object' && module && !module.nodeType && module;

  /** Detect the popular CommonJS extension `module.exports`. */
  var moduleExports = freeModule && freeModule.exports === freeExports;

  /** Detect free variable `process` from Node.js. */
  var freeProcess = moduleExports && freeGlobal.process;

  /** Used to access faster Node.js helpers. */
  var nodeUtil = (function() {
    try {
      // Use `util.types` for Node.js 10+.
      var types = freeModule && freeModule.require && freeModule.require('util').types;

      if (types) {
        return types;
      }

      // Legacy `process.binding('util')` for Node.js < 10.
      return freeProcess && freeProcess.binding && freeProcess.binding('util');
    } catch (e) {}
  }());

  /* Node.js helper references. */
  var nodeIsArrayBuffer = nodeUtil && nodeUtil.isArrayBuffer,
      nodeIsDate = nodeUtil && nodeUtil.isDate,
      nodeIsMap = nodeUtil && nodeUtil.isMap,
      nodeIsRegExp = nodeUtil && nodeUtil.isRegExp,
      nodeIsSet = nodeUtil && nodeUtil.isSet,
      nodeIsTypedArray = nodeUtil && nodeUtil.isTypedArray;

  /*--------------------------------------------------------------------------*/

  /**
   * A faster alternative to `Function#apply`, this function invokes `func`
   * with the `this` binding of `thisArg` and the arguments of `args`.
   *
   * @private
   * @param {Function} func The function to invoke.
   * @param {*} thisArg The `this` binding of `func`.
   * @param {Array} args The arguments to invoke `func` with.
   * @returns {*} Returns the result of `func`.
   */
  function apply(func, thisArg, args) {
    switch (args.length) {
      case 0: return func.call(thisArg);
      case 1: return func.call(thisArg, args[0]);
      case 2: return func.call(thisArg, args[0], args[1]);
      case 3: return func.call(thisArg, args[0], args[1], args[2]);
    }
    return func.apply(thisArg, args);
  }

  /**
   * A specialized version of `baseAggregator` for arrays.
   *
   * @private
   * @param {Array} [array] The array to iterate over.
   * @param {Function} setter The function to set `accumulator` values.
   * @param {Function} iteratee The iteratee to transform keys.
   * @param {Object} accumulator The initial aggregated object.
   * @returns {Function} Returns `accumulator`.
   */
  function arrayAggregator(array, setter, iteratee, accumulator) {
    var index = -1,
        length = array == null ? 0 : array.length;

    while (++index < length) {
      var value = array[index];
      setter(accumulator, value, iteratee(value), array);
    }
    return accumulator;
  }

  /**
   * A specialized version of `_.forEach` for arrays without support for
   * iteratee shorthands.
   *
   * @private
   * @param {Array} [array] The array to iterate over.
   * @param {Function} iteratee The function invoked per iteration.
   * @returns {Array} Returns `array`.
   */
  function arrayEach(array, iteratee) {
    var index = -1,
        length = array == null ? 0 : array.length;

    while (++index < length) {
      if (iteratee(array[index], index, array) === false) {
        break;
      }
    }
    return array;
  }

  /**
   * A specialized version of `_.forEachRight` for arrays without support for
   * iteratee shorthands.
   *
   * @private
   * @param {Array} [array] The array to iterate over.
   * @param {Function} iteratee The function invoked per iteration.
   * @returns {Array} Returns `array`.
   */
  function arrayEachRight(array, iteratee) {
    var length = array == null ? 0 : array.length;

    while (length--) {
      if (iteratee(array[length], length, array) === false) {
        break;
      }
    }
    return array;
  }

  /**
   * A specialized version of `_.every` for arrays without support for
   * iteratee shorthands.
   *
   * @private
   * @param {Array} [array] The array to iterate over.
   * @param {Function} predicate The function invoked per iteration.
   * @returns {boolean} Returns `true` if all elements pass the predicate check,
   *  else `false`.
   */
  function arrayEvery(array, predicate) {
    var index = -1,
        length = array == null ? 0 : array.length;

    while (++index < length) {
      if (!predicate(array[index], index, array)) {
        return false;
      }
    }
    return true;
  }

  /**
   * A specialized version of `_.filter` for arrays without support for
   * iteratee shorthands.
   *
   * @private
   * @param {Array} [array] The array to iterate over.
   * @param {Function} predicate The function invoked per iteration.
   * @returns {Array} Returns the new filtered array.
   */
  function arrayFilter(array, predicate) {
    var index = -1,
        length = array == null ? 0 : array.length,
        resIndex = 0,
        result = [];

    while (++index < length) {
      var value = array[index];
      if (predicate(value, index, array)) {
        result[resIndex++] = value;
      }
    }
    return result;
  }

  /**
   * A specialized version of `_.includes` for arrays without support for
   * specifying an index to search from.
   *
   * @private
   * @param {Array} [array] The array to inspect.
   * @param {*} target The value to search for.
   * @returns {boolean} Returns `true` if `target` is found, else `false`.
   */
  function arrayIncludes(array, value) {
    var length = array == null ? 0 : array.length;
    return !!length && baseIndexOf(array, value, 0) > -1;
  }

  /**
   * This function is like `arrayIncludes` except that it accepts a comparator.
   *
   * @private
   * @param {Array} [array] The array to inspect.
   * @param {*} target The value to search for.
   * @param {Function} comparator The comparator invoked per element.
   * @returns {boolean} Returns `true` if `target` is found, else `false`.
   */
  function arrayIncludesWith(array, value, comparator) {
    var index = -1,
        length = array == null ? 0 : array.length;

    while (++index < length) {
      if (comparator(value, array[index])) {
        return true;
      }
    }
    return false;
  }

  /**
   * A specialized version of `_.map` for arrays without support for iteratee
   * shorthands.
   *
   * @private
   * @param {Array} [array] The array to iterate over.
   * @param {Function} iteratee The function invoked per iteration.
   * @returns {Array} Returns the new mapped array.
   */
  function arrayMap(array, iteratee) {
    var index = -1,
        length = array == null ? 0 : array.length,
        result = Array(length);

    while (++index < length) {
      result[index] = iteratee(array[index], index, array);
    }
    return result;
  }

  /**
   * Appends the elements of `values` to `array`.
   *
   * @private
   * @param {Array} array The array to modify.
   * @param {Array} values The values to append.
   * @returns {Array} Returns `array`.
   */
  function arrayPush(array, values) {
    var index = -1,
        length = values.length,
        offset = array.length;

    while (++index < length) {
      array[offset + index] = values[index];
    }
    return array;
  }

  /**
   * A specialized version of `_.reduce` for arrays without support for
   * iteratee shorthands.
   *
   * @private
   * @param {Array} [array] The array to iterate over.
   * @param {Function} iteratee The function invoked per iteration.
   * @param {*} [accumulator] The initial value.
   * @param {boolean} [initAccum] Specify using the first element of `array` as
   *  the initial value.
   * @returns {*} Returns the accumulated value.
   */
  function arrayReduce(array, iteratee, accumulator, initAccum) {
    var index = -1,
        length = array == null ? 0 : array.length;

    if (initAccum && length) {
      accumulator = array[++index];
    }
    while (++index < length) {
      accumulator = iteratee(accumulator, array[index], index, array);
    }
    return accumulator;
  }

  /**
   * A specialized version of `_.reduceRight` for arrays without support for
   * iteratee shorthands.
   *
   * @private
   * @param {Array} [array] The array to iterate over.
   * @param {Function} iteratee The function invoked per iteration.
   * @param {*} [accumulator] The initial value.
   * @param {boolean} [initAccum] Specify using the last element of `array` as
   *  the initial value.
   * @returns {*} Returns the accumulated value.
   */
  function arrayReduceRight(array, iteratee, accumulator, initAccum) {
    var length = array == null ? 0 : array.length;
    if (initAccum && length) {
      accumulator = array[--length];
    }
    while (length--) {
      accumulator = iteratee(accumulator, array[length], length, array);
    }
    return accumulator;
  }

  /**
   * A specialized version of `_.some` for arrays without support for iteratee
   * shorthands.
   *
   * @private
   * @param {Array} [array] The array to iterate over.
   * @param {Function} predicate The function invoked per iteration.
   * @returns {boolean} Returns `true` if any element passes the predicate check,
   *  else `false`.
   */
  function arraySome(array, predicate) {
    var index = -1,
        length = array == null ? 0 : array.length;

    while (++index < length) {
      if (predicate(array[index], index, array)) {
        return true;
      }
    }
    return false;
  }

  /**
   * Gets the size of an ASCII `string`.
   *
   * @private
   * @param {string} string The string inspect.
   * @returns {number} Returns the string size.
   */
  var asciiSize = baseProperty('length');

  /**
   * Converts an ASCII `string` to an array.
   *
   * @private
   * @param {string} string The string to convert.
   * @returns {Array} Returns the converted array.
   */
  function asciiToArray(string) {
    return string.split('');
  }

  /**
   * Splits an ASCII `string` into an array of its words.
   *
   * @private
   * @param {string} The string to inspect.
   * @returns {Array} Returns the words of `string`.
   */
  function asciiWords(string) {
    return string.match(reAsciiWord) || [];
  }

  /**
   * The base implementation of methods like `_.findKey` and `_.findLastKey`,
   * without support for iteratee shorthands, which iterates over `collection`
   * using `eachFunc`.
   *
   * @private
   * @param {Array|Object} collection The collection to inspect.
   * @param {Function} predicate The function invoked per iteration.
   * @param {Function} eachFunc The function to iterate over `collection`.
   * @returns {*} Returns the found element or its key, else `undefined`.
   */
  function baseFindKey(collection, predicate, eachFunc) {
    var result;
    eachFunc(collection, function(value, key, collection) {
      if (predicate(value, key, collection)) {
        result = key;
        return false;
      }
    });
    return result;
  }

  /**
   * The base implementation of `_.findIndex` and `_.findLastIndex` without
   * support for iteratee shorthands.
   *
   * @private
   * @param {Array} array The array to inspect.
   * @param {Function} predicate The function invoked per iteration.
   * @param {number} fromIndex The index to search from.
   * @param {boolean} [fromRight] Specify iterating from right to left.
   * @returns {number} Returns the index of the matched value, else `-1`.
   */
  function baseFindIndex(array, predicate, fromIndex, fromRight) {
    var length = array.length,
        index = fromIndex + (fromRight ? 1 : -1);

    while ((fromRight ? index-- : ++index < length)) {
      if (predicate(array[index], index, array)) {
        return index;
      }
    }
    return -1;
  }

  /**
   * The base implementation of `_.indexOf` without `fromIndex` bounds checks.
   *
   * @private
   * @param {Array} array The array to inspect.
   * @param {*} value The value to search for.
   * @param {number} fromIndex The index to search from.
   * @returns {number} Returns the index of the matched value, else `-1`.
   */
  function baseIndexOf(array, value, fromIndex) {
    return value === value
      ? strictIndexOf(array, value, fromIndex)
      : baseFindIndex(array, baseIsNaN, fromIndex);
  }

  /**
   * This function is like `baseIndexOf` except that it accepts a comparator.
   *
   * @private
   * @param {Array} array The array to inspect.
   * @param {*} value The value to search for.
   * @param {number} fromIndex The index to search from.
   * @param {Function} comparator The comparator invoked per element.
   * @returns {number} Returns the index of the matched value, else `-1`.
   */
  function baseIndexOfWith(array, value, fromIndex, comparator) {
    var index = fromIndex - 1,
        length = array.length;

    while (++index < length) {
      if (comparator(array[index], value)) {
        return index;
      }
    }
    return -1;
  }

  /**
   * The base implementation of `_.isNaN` without support for number objects.
   *
   * @private
   * @param {*} value The value to check.
   * @returns {boolean} Returns `true` if `value` is `NaN`, else `false`.
   */
  function baseIsNaN(value) {
    return value !== value;
  }

  /**
   * The base implementation of `_.mean` and `_.meanBy` without support for
   * iteratee shorthands.
   *
   * @private
   * @param {Array} array The array to iterate over.
   * @param {Function} iteratee The function invoked per iteration.
   * @returns {number} Returns the mean.
   */
  function baseMean(array, iteratee) {
    var length = array == null ? 0 : array.length;
    return length ? (baseSum(array, iteratee) / length) : NAN;
  }

  /**
   * The base implementation of `_.property` without support for deep paths.
   *
   * @private
   * @param {string} key The key of the property to get.
   * @returns {Function} Returns the new accessor function.
   */
  function baseProperty(key) {
    return function(object) {
      return object == null ? undefined : object[key];
    };
  }

  /**
   * The base implementation of `_.propertyOf` without support for deep paths.
   *
   * @private
   * @param {Object} object The object to query.
   * @returns {Function} Returns the new accessor function.
   */
  function basePropertyOf(object) {
    return function(key) {
      return object == null ? undefined : object[key];
    };
  }

  /**
   * The base implementation of `_.reduce` and `_.reduceRight`, without support
   * for iteratee shorthands, which iterates over `collection` using `eachFunc`.
   *
   * @private
   * @param {Array|Object} collection The collection to iterate over.
   * @param {Function} iteratee The function invoked per iteration.
   * @param {*} accumulator The initial value.
   * @param {boolean} initAccum Specify using the first or last element of
   *  `collection` as the initial value.
   * @param {Function} eachFunc The function to iterate over `collection`.
   * @returns {*} Returns the accumulated value.
   */
  function baseReduce(collection, iteratee, accumulator, initAccum, eachFunc) {
    eachFunc(collection, function(value, index, collection) {
      accumulator = initAccum
        ? (initAccum = false, value)
        : iteratee(accumulator, value, index, collection);
    });
    return accumulator;
  }

  /**
   * The base implementation of `_.sortBy` which uses `comparer` to define the
   * sort order of `array` and replaces criteria objects with their corresponding
   * values.
   *
   * @private
   * @param {Array} array The array to sort.
   * @param {Function} comparer The function to define sort order.
   * @returns {Array} Returns `array`.
   */
  function baseSortBy(array, comparer) {
    var length = array.length;

    array.sort(comparer);
    while (length--) {
      array[length] = array[length].value;
    }
    return array;
  }

  /**
   * The base implementation of `_.sum` and `_.sumBy` without support for
   * iteratee shorthands.
   *
   * @private
   * @param {Array} array The array to iterate over.
   * @param {Function} iteratee The function invoked per iteration.
   * @returns {number} Returns the sum.
   */
  function baseSum(array, iteratee) {
    var result,
        index = -1,
        length = array.length;

    while (++index < length) {
      var current = iteratee(array[index]);
      if (current !== undefined) {
        result = result === undefined ? current : (result + current);
      }
    }
    return result;
  }

  /**
   * The base implementation of `_.times` without support for iteratee shorthands
   * or max array length checks.
   *
   * @private
   * @param {number} n The number of times to invoke `iteratee`.
   * @param {Function} iteratee The function invoked per iteration.
   * @returns {Array} Returns the array of results.
   */
  function baseTimes(n, iteratee) {
    var index = -1,
        result = Array(n);

    while (++index < n) {
      result[index] = iteratee(index);
    }
    return result;
  }

  /**
   * The base implementation of `_.toPairs` and `_.toPairsIn` which creates an array
   * of key-value pairs for `object` corresponding to the property names of `props`.
   *
   * @private
   * @param {Object} object The object to query.
   * @param {Array} props The property names to get values for.
   * @returns {Object} Returns the key-value pairs.
   */
  function baseToPairs(object, props) {
    return arrayMap(props, function(key) {
      return [key, object[key]];
    });
  }

  /**
   * The base implementation of `_.trim`.
   *
   * @private
   * @param {string} string The string to trim.
   * @returns {string} Returns the trimmed string.
   */
  function baseTrim(string) {
    return string
      ? string.slice(0, trimmedEndIndex(string) + 1).replace(reTrimStart, '')
      : string;
  }

  /**
   * The base implementation of `_.unary` without support for storing metadata.
   *
   * @private
   * @param {Function} func The function to cap arguments for.
   * @returns {Function} Returns the new capped function.
   */
  function baseUnary(func) {
    return function(value) {
      return func(value);
    };
  }

  /**
   * The base implementation of `_.values` and `_.valuesIn` which creates an
   * array of `object` property values corresponding to the property names
   * of `props`.
   *
   * @private
   * @param {Object} object The object to query.
   * @param {Array} props The property names to get values for.
   * @returns {Object} Returns the array of property values.
   */
  function baseValues(object, props) {
    return arrayMap(props, function(key) {
      return object[key];
    });
  }

  /**
   * Checks if a `cache` value for `key` exists.
   *
   * @private
   * @param {Object} cache The cache to query.
   * @param {string} key The key of the entry to check.
   * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
   */
  function cacheHas(cache, key) {
    return cache.has(key);
  }

  /**
   * Used by `_.trim` and `_.trimStart` to get the index of the first string symbol
   * that is not found in the character symbols.
   *
   * @private
   * @param {Array} strSymbols The string symbols to inspect.
   * @param {Array} chrSymbols The character symbols to find.
   * @returns {number} Returns the index of the first unmatched string symbol.
   */
  function charsStartIndex(strSymbols, chrSymbols) {
    var index = -1,
        length = strSymbols.length;

    while (++index < length && baseIndexOf(chrSymbols, strSymbols[index], 0) > -1) {}
    return index;
  }

  /**
   * Used by `_.trim` and `_.trimEnd` to get the index of the last string symbol
   * that is not found in the character symbols.
   *
   * @private
   * @param {Array} strSymbols The string symbols to inspect.
   * @param {Array} chrSymbols The character symbols to find.
   * @returns {number} Returns the index of the last unmatched string symbol.
   */
  function charsEndIndex(strSymbols, chrSymbols) {
    var index = strSymbols.length;

    while (index-- && baseIndexOf(chrSymbols, strSymbols[index], 0) > -1) {}
    return index;
  }

  /**
   * Gets the number of `placeholder` occurrences in `array`.
   *
   * @private
   * @param {Array} array The array to inspect.
   * @param {*} placeholder The placeholder to search for.
   * @returns {number} Returns the placeholder count.
   */
  function countHolders(array, placeholder) {
    var length = array.length,
        result = 0;

    while (length--) {
      if (array[length] === placeholder) {
        ++result;
      }
    }
    return result;
  }

  /**
   * Used by `_.deburr` to convert Latin-1 Supplement and Latin Extended-A
   * letters to basic Latin letters.
   *
   * @private
   * @param {string} letter The matched letter to deburr.
   * @returns {string} Returns the deburred letter.
   */
  var deburrLetter = basePropertyOf(deburredLetters);

  /**
   * Used by `_.escape` to convert characters to HTML entities.
   *
   * @private
   * @param {string} chr The matched character to escape.
   * @returns {string} Returns the escaped character.
   */
  var escapeHtmlChar = basePropertyOf(htmlEscapes);

  /**
   * Used by `_.template` to escape characters for inclusion in compiled string literals.
   *
   * @private
   * @param {string} chr The matched character to escape.
   * @returns {string} Returns the escaped character.
   */
  function escapeStringChar(chr) {
    return '\\' + stringEscapes[chr];
  }

  /**
   * Gets the value at `key` of `object`.
   *
   * @private
   * @param {Object} [object] The object to query.
   * @param {string} key The key of the property to get.
   * @returns {*} Returns the property value.
   */
  function getValue(object, key) {
    return object == null ? undefined : object[key];
  }

  /**
   * Checks if `string` contains Unicode symbols.
   *
   * @private
   * @param {string} string The string to inspect.
   * @returns {boolean} Returns `true` if a symbol is found, else `false`.
   */
  function hasUnicode(string) {
    return reHasUnicode.test(string);
  }

  /**
   * Checks if `string` contains a word composed of Unicode symbols.
   *
   * @private
   * @param {string} string The string to inspect.
   * @returns {boolean} Returns `true` if a word is found, else `false`.
   */
  function hasUnicodeWord(string) {
    return reHasUnicodeWord.test(string);
  }

  /**
   * Converts `iterator` to an array.
   *
   * @private
   * @param {Object} iterator The iterator to convert.
   * @returns {Array} Returns the converted array.
   */
  function iteratorToArray(iterator) {
    var data,
        result = [];

    while (!(data = iterator.next()).done) {
      result.push(data.value);
    }
    return result;
  }

  /**
   * Converts `map` to its key-value pairs.
   *
   * @private
   * @param {Object} map The map to convert.
   * @returns {Array} Returns the key-value pairs.
   */
  function mapToArray(map) {
    var index = -1,
        result = Array(map.size);

    map.forEach(function(value, key) {
      result[++index] = [key, value];
    });
    return result;
  }

  /**
   * Creates a unary function that invokes `func` with its argument transformed.
   *
   * @private
   * @param {Function} func The function to wrap.
   * @param {Function} transform The argument transform.
   * @returns {Function} Returns the new function.
   */
  function overArg(func, transform) {
    return function(arg) {
      return func(transform(arg));
    };
  }

  /**
   * Replaces all `placeholder` elements in `array` with an internal placeholder
   * and returns an array of their indexes.
   *
   * @private
   * @param {Array} array The array to modify.
   * @param {*} placeholder The placeholder to replace.
   * @returns {Array} Returns the new array of placeholder indexes.
   */
  function replaceHolders(array, placeholder) {
    var index = -1,
        length = array.length,
        resIndex = 0,
        result = [];

    while (++index < length) {
      var value = array[index];
      if (value === placeholder || value === PLACEHOLDER) {
        array[index] = PLACEHOLDER;
        result[resIndex++] = index;
      }
    }
    return result;
  }

  /**
   * Converts `set` to an array of its values.
   *
   * @private
   * @param {Object} set The set to convert.
   * @returns {Array} Returns the values.
   */
  function setToArray(set) {
    var index = -1,
        result = Array(set.size);

    set.forEach(function(value) {
      result[++index] = value;
    });
    return result;
  }

  /**
   * Converts `set` to its value-value pairs.
   *
   * @private
   * @param {Object} set The set to convert.
   * @returns {Array} Returns the value-value pairs.
   */
  function setToPairs(set) {
    var index = -1,
        result = Array(set.size);

    set.forEach(function(value) {
      result[++index] = [value, value];
    });
    return result;
  }

  /**
   * A specialized version of `_.indexOf` which performs strict equality
   * comparisons of values, i.e. `===`.
   *
   * @private
   * @param {Array} array The array to inspect.
   * @param {*} value The value to search for.
   * @param {number} fromIndex The index to search from.
   * @returns {number} Returns the index of the matched value, else `-1`.
   */
  function strictIndexOf(array, value, fromIndex) {
    var index = fromIndex - 1,
        length = array.length;

    while (++index < length) {
      if (array[index] === value) {
        return index;
      }
    }
    return -1;
  }

  /**
   * A specialized version of `_.lastIndexOf` which performs strict equality
   * comparisons of values, i.e. `===`.
   *
   * @private
   * @param {Array} array The array to inspect.
   * @param {*} value The value to search for.
   * @param {number} fromIndex The index to search from.
   * @returns {number} Returns the index of the matched value, else `-1`.
   */
  function strictLastIndexOf(array, value, fromIndex) {
    var index = fromIndex + 1;
    while (index--) {
      if (array[index] === value) {
        return index;
      }
    }
    return index;
  }

  /**
   * Gets the number of symbols in `string`.
   *
   * @private
   * @param {string} string The string to inspect.
   * @returns {number} Returns the string size.
   */
  function stringSize(string) {
    return hasUnicode(string)
      ? unicodeSize(string)
      : asciiSize(string);
  }

  /**
   * Converts `string` to an array.
   *
   * @private
   * @param {string} string The string to convert.
   * @returns {Array} Returns the converted array.
   */
  function stringToArray(string) {
    return hasUnicode(string)
      ? unicodeToArray(string)
      : asciiToArray(string);
  }

  /**
   * Used by `_.trim` and `_.trimEnd` to get the index of the last non-whitespace
   * character of `string`.
   *
   * @private
   * @param {string} string The string to inspect.
   * @returns {number} Returns the index of the last non-whitespace character.
   */
  function trimmedEndIndex(string) {
    var index = string.length;

    while (index-- && reWhitespace.test(string.charAt(index))) {}
    return index;
  }

  /**
   * Used by `_.unescape` to convert HTML entities to characters.
   *
   * @private
   * @param {string} chr The matched character to unescape.
   * @returns {string} Returns the unescaped character.
   */
  var unescapeHtmlChar = basePropertyOf(htmlUnescapes);

  /**
   * Gets the size of a Unicode `string`.
   *
   * @private
   * @param {string} string The string inspect.
   * @returns {number} Returns the string size.
   */
  function unicodeSize(string) {
    var result = reUnicode.lastIndex = 0;
    while (reUnicode.test(string)) {
      ++result;
    }
    return result;
  }

  /**
   * Converts a Unicode `string` to an array.
   *
   * @private
   * @param {string} string The string to convert.
   * @returns {Array} Returns the converted array.
   */
  function unicodeToArray(string) {
    return string.match(reUnicode) || [];
  }

  /**
   * Splits a Unicode `string` into an array of its words.
   *
   * @private
   * @param {string} The string to inspect.
   * @returns {Array} Returns the words of `string`.
   */
  function unicodeWords(string) {
    return string.match(reUnicodeWord) || [];
  }

  /*--------------------------------------------------------------------------*/

  /**
   * Create a new pristine `lodash` function using the `context` object.
   *
   * @static
   * @memberOf _
   * @since 1.1.0
   * @category Util
   * @param {Object} [context=root] The context object.
   * @returns {Function} Returns a new `lodash` function.
   * @example
   *
   * _.mixin({ 'foo': _.constant('foo') });
   *
   * var lodash = _.runInContext();
   * lodash.mixin({ 'bar': lodash.constant('bar') });
   *
   * _.isFunction(_.foo);
   * // => true
   * _.isFunction(_.bar);
   * // => false
   *
   * lodash.isFunction(lodash.foo);
   * // => false
   * lodash.isFunction(lodash.bar);
   * // => true
   *
   * // Create a suped-up `defer` in Node.js.
   * var defer = _.runInContext({ 'setTimeout': setImmediate }).defer;
   */
  var runInContext = (function runInContext(context) {
    context = context == null ? root : _.defaults(root.Object(), context, _.pick(root, contextProps));

    /** Built-in constructor references. */
    var Array = context.Array,
        Date = context.Date,
        Error = context.Error,
        Function = context.Function,
        Math = context.Math,
        Object = context.Object,
        RegExp = context.RegExp,
        String = context.String,
        TypeError = context.TypeError;

    /** Used for built-in method references. */
    var arrayProto = Array.prototype,
        funcProto = Function.prototype,
        objectProto = Object.prototype;

    /** Used to detect overreaching core-js shims. */
    var coreJsData = context['__core-js_shared__'];

    /** Used to resolve the decompiled source of functions. */
    var funcToString = funcProto.toString;

    /** Used to check objects for own properties. */
    var hasOwnProperty = objectProto.hasOwnProperty;

    /** Used to generate unique IDs. */
    var idCounter = 0;

    /** Used to detect methods masquerading as native. */
    var maskSrcKey = (function() {
      var uid = /[^.]+$/.exec(coreJsData && coreJsData.keys && coreJsData.keys.IE_PROTO || '');
      return uid ? ('Symbol(src)_1.' + uid) : '';
    }());

    /**
     * Used to resolve the
     * [`toStringTag`](http://ecma-international.org/ecma-262/7.0/#sec-object.prototype.tostring)
     * of values.
     */
    var nativeObjectToString = objectProto.toString;

    /** Used to infer the `Object` constructor. */
    var objectCtorString = funcToString.call(Object);

    /** Used to restore the original `_` reference in `_.noConflict`. */
    var oldDash = root._;

    /** Used to detect if a method is native. */
    var reIsNative = RegExp('^' +
      funcToString.call(hasOwnProperty).replace(reRegExpChar, '\\$&')
      .replace(/hasOwnProperty|(function).*?(?=\\\()| for .+?(?=\\\])/g, '$1.*?') + '$'
    );

    /** Built-in value references. */
    var Buffer = moduleExports ? context.Buffer : undefined,
        Symbol = context.Symbol,
        Uint8Array = context.Uint8Array,
        allocUnsafe = Buffer ? Buffer.allocUnsafe : undefined,
        getPrototype = overArg(Object.getPrototypeOf, Object),
        objectCreate = Object.create,
        propertyIsEnumerable = objectProto.propertyIsEnumerable,
        splice = arrayProto.splice,
        spreadableSymbol = Symbol ? Symbol.isConcatSpreadable : undefined,
        symIterator = Symbol ? Symbol.iterator : undefined,
        symToStringTag = Symbol ? Symbol.toStringTag : undefined;

    var defineProperty = (function() {
      try {
        var func = getNative(Object, 'defineProperty');
        func({}, '', {});
        return func;
      } catch (e) {}
    }());

    /** Mocked built-ins. */
    var ctxClearTimeout = context.clearTimeout !== root.clearTimeout && context.clearTimeout,
        ctxNow = Date && Date.now !== root.Date.now && Date.now,
        ctxSetTimeout = context.setTimeout !== root.setTimeout && context.setTimeout;

    /* Built-in method references for those with the same name as other `lodash` methods. */
    var nativeCeil = Math.ceil,
        nativeFloor = Math.floor,
        nativeGetSymbols = Object.getOwnPropertySymbols,
        nativeIsBuffer = Buffer ? Buffer.isBuffer : undefined,
        nativeIsFinite = context.isFinite,
        nativeJoin = arrayProto.join,
        nativeKeys = overArg(Object.keys, Object),
        nativeMax = Math.max,
        nativeMin = Math.min,
        nativeNow = Date.now,
        nativeParseInt = context.parseInt,
        nativeRandom = Math.random,
        nativeReverse = arrayProto.reverse;

    /* Built-in method references that are verified to be native. */
    var DataView = getNative(context, 'DataView'),
        Map = getNative(context, 'Map'),
        Promise = getNative(context, 'Promise'),
        Set = getNative(context, 'Set'),
        WeakMap = getNative(context, 'WeakMap'),
        nativeCreate = getNative(Object, 'create');

    /** Used to store function metadata. */
    var metaMap = WeakMap && new WeakMap;

    /** Used to lookup unminified function names. */
    var realNames = {};

    /** Used to detect maps, sets, and weakmaps. */
    var dataViewCtorString = toSource(DataView),
        mapCtorString = toSource(Map),
        promiseCtorString = toSource(Promise),
        setCtorString = toSource(Set),
        weakMapCtorString = toSource(WeakMap);

    /** Used to convert symbols to primitives and strings. */
    var symbolProto = Symbol ? Symbol.prototype : undefined,
        symbolValueOf = symbolProto ? symbolProto.valueOf : undefined,
        symbolToString = symbolProto ? symbolProto.toString : undefined;

    /*------------------------------------------------------------------------*/

    /**
     * Creates a `lodash` object which wraps `value` to enable implicit method
     * chain sequences. Methods that operate on and return arrays, collections,
     * and functions can be chained together. Methods that retrieve a single value
     * or may return a primitive value will automatically end the chain sequence
     * and return the unwrapped value. Otherwise, the value must be unwrapped
     * with `_#value`.
     *
     * Explicit chain sequences, which must be unwrapped with `_#value`, may be
     * enabled using `_.chain`.
     *
     * The execution of chained methods is lazy, that is, it's deferred until
     * `_#value` is implicitly or explicitly called.
     *
     * Lazy evaluation allows several methods to support shortcut fusion.
     * Shortcut fusion is an optimization to merge iteratee calls; this avoids
     * the creation of intermediate arrays and can greatly reduce the number of
     * iteratee executions. Sections of a chain sequence qualify for shortcut
     * fusion if the section is applied to an array and iteratees accept only
     * one argument. The heuristic for whether a section qualifies for shortcut
     * fusion is subject to change.
     *
     * Chaining is supported in custom builds as long as the `_#value` method is
     * directly or indirectly included in the build.
     *
     * In addition to lodash methods, wrappers have `Array` and `String` methods.
     *
     * The wrapper `Array` methods are:
     * `concat`, `join`, `pop`, `push`, `shift`, `sort`, `splice`, and `unshift`
     *
     * The wrapper `String` methods are:
     * `replace` and `split`
     *
     * The wrapper methods that support shortcut fusion are:
     * `at`, `compact`, `drop`, `dropRight`, `dropWhile`, `filter`, `find`,
     * `findLast`, `head`, `initial`, `last`, `map`, `reject`, `reverse`, `slice`,
     * `tail`, `take`, `takeRight`, `takeRightWhile`, `takeWhile`, and `toArray`
     *
     * The chainable wrapper methods are:
     * `after`, `ary`, `assign`, `assignIn`, `assignInWith`, `assignWith`, `at`,
     * `before`, `bind`, `bindAll`, `bindKey`, `castArray`, `chain`, `chunk`,
     * `commit`, `compact`, `concat`, `conforms`, `constant`, `countBy`, `create`,
     * `curry`, `debounce`, `defaults`, `defaultsDeep`, `defer`, `delay`,
     * `difference`, `differenceBy`, `differenceWith`, `drop`, `dropRight`,
     * `dropRightWhile`, `dropWhile`, `extend`, `extendWith`, `fill`, `filter`,
     * `flatMap`, `flatMapDeep`, `flatMapDepth`, `flatten`, `flattenDeep`,
     * `flattenDepth`, `flip`, `flow`, `flowRight`, `fromPairs`, `functions`,
     * `functionsIn`, `groupBy`, `initial`, `intersection`, `intersectionBy`,
     * `intersectionWith`, `invert`, `invertBy`, `invokeMap`, `iteratee`, `keyBy`,
     * `keys`, `keysIn`, `map`, `mapKeys`, `mapValues`, `matches`, `matchesProperty`,
     * `memoize`, `merge`, `mergeWith`, `method`, `methodOf`, `mixin`, `negate`,
     * `nthArg`, `omit`, `omitBy`, `once`, `orderBy`, `over`, `overArgs`,
     * `overEvery`, `overSome`, `partial`, `partialRight`, `partition`, `pick`,
     * `pickBy`, `plant`, `property`, `propertyOf`, `pull`, `pullAll`, `pullAllBy`,
     * `pullAllWith`, `pullAt`, `push`, `range`, `rangeRight`, `rearg`, `reject`,
     * `remove`, `rest`, `reverse`, `sampleSize`, `set`, `setWith`, `shuffle`,
     * `slice`, `sort`, `sortBy`, `splice`, `spread`, `tail`, `take`, `takeRight`,
     * `takeRightWhile`, `takeWhile`, `tap`, `throttle`, `thru`, `toArray`,
     * `toPairs`, `toPairsIn`, `toPath`, `toPlainObject`, `transform`, `unary`,
     * `union`, `unionBy`, `unionWith`, `uniq`, `uniqBy`, `uniqWith`, `unset`,
     * `unshift`, `unzip`, `unzipWith`, `update`, `updateWith`, `values`,
     * `valuesIn`, `without`, `wrap`, `xor`, `xorBy`, `xorWith`, `zip`,
     * `zipObject`, `zipObjectDeep`, and `zipWith`
     *
     * The wrapper methods that are **not** chainable by default are:
     * `add`, `attempt`, `camelCase`, `capitalize`, `ceil`, `clamp`, `clone`,
     * `cloneDeep`, `cloneDeepWith`, `cloneWith`, `conformsTo`, `deburr`,
     * `defaultTo`, `divide`, `each`, `eachRight`, `endsWith`, `eq`, `escape`,
     * `escapeRegExp`, `every`, `find`, `findIndex`, `findKey`, `findLast`,
     * `findLastIndex`, `findLastKey`, `first`, `floor`, `forEach`, `forEachRight`,
     * `forIn`, `forInRight`, `forOwn`, `forOwnRight`, `get`, `gt`, `gte`, `has`,
     * `hasIn`, `head`, `identity`, `includes`, `indexOf`, `inRange`, `invoke`,
     * `isArguments`, `isArray`, `isArrayBuffer`, `isArrayLike`, `isArrayLikeObject`,
     * `isBoolean`, `isBuffer`, `isDate`, `isElement`, `isEmpty`, `isEqual`,
     * `isEqualWith`, `isError`, `isFinite`, `isFunction`, `isInteger`, `isLength`,
     * `isMap`, `isMatch`, `isMatchWith`, `isNaN`, `isNative`, `isNil`, `isNull`,
     * `isNumber`, `isObject`, `isObjectLike`, `isPlainObject`, `isRegExp`,
     * `isSafeInteger`, `isSet`, `isString`, `isUndefined`, `isTypedArray`,
     * `isWeakMap`, `isWeakSet`, `join`, `kebabCase`, `last`, `lastIndexOf`,
     * `lowerCase`, `lowerFirst`, `lt`, `lte`, `max`, `maxBy`, `mean`, `meanBy`,
     * `min`, `minBy`, `multiply`, `noConflict`, `noop`, `now`, `nth`, `pad`,
     * `padEnd`, `padStart`, `parseInt`, `pop`, `random`, `reduce`, `reduceRight`,
     * `repeat`, `result`, `round`, `runInContext`, `sample`, `shift`, `size`,
     * `snakeCase`, `some`, `sortedIndex`, `sortedIndexBy`, `sortedLastIndex`,
     * `sortedLastIndexBy`, `startCase`, `startsWith`, `stubArray`, `stubFalse`,
     * `stubObject`, `stubString`, `stubTrue`, `subtract`, `sum`, `sumBy`,
     * `template`, `times`, `toFinite`, `toInteger`, `toJSON`, `toLength`,
     * `toLower`, `toNumber`, `toSafeInteger`, `toString`, `toUpper`, `trim`,
     * `trimEnd`, `trimStart`, `truncate`, `unescape`, `uniqueId`, `upperCase`,
     * `upperFirst`, `value`, and `words`
     *
     * @name _
     * @constructor
     * @category Seq
     * @param {*} value The value to wrap in a `lodash` instance.
     * @returns {Object} Returns the new `lodash` wrapper instance.
     * @example
     *
     * function square(n) {
     *   return n * n;
     * }
     *
     * var wrapped = _([1, 2, 3]);
     *
     * // Returns an unwrapped value.
     * wrapped.reduce(_.add);
     * // => 6
     *
     * // Returns a wrapped value.
     * var squares = wrapped.map(square);
     *
     * _.isArray(squares);
     * // => false
     *
     * _.isArray(squares.value());
     * // => true
     */
    function lodash(value) {
      if (isObjectLike(value) && !isArray(value) && !(value instanceof LazyWrapper)) {
        if (value instanceof LodashWrapper) {
          return value;
        }
        if (hasOwnProperty.call(value, '__wrapped__')) {
          return wrapperClone(value);
        }
      }
      return new LodashWrapper(value);
    }

    /**
     * The base implementation of `_.create` without support for assigning
     * properties to the created object.
     *
     * @private
     * @param {Object} proto The object to inherit from.
     * @returns {Object} Returns the new object.
     */
    var baseCreate = (function() {
      function object() {}
      return function(proto) {
        if (!isObject(proto)) {
          return {};
        }
        if (objectCreate) {
          return objectCreate(proto);
        }
        object.prototype = proto;
        var result = new object;
        object.prototype = undefined;
        return result;
      };
    }());

    /**
     * The function whose prototype chain sequence wrappers inherit from.
     *
     * @private
     */
    function baseLodash() {
      // No operation performed.
    }

    /**
     * The base constructor for creating `lodash` wrapper objects.
     *
     * @private
     * @param {*} value The value to wrap.
     * @param {boolean} [chainAll] Enable explicit method chain sequences.
     */
    function LodashWrapper(value, chainAll) {
      this.__wrapped__ = value;
      this.__actions__ = [];
      this.__chain__ = !!chainAll;
      this.__index__ = 0;
      this.__values__ = undefined;
    }

    /**
     * By default, the template delimiters used by lodash are like those in
     * embedded Ruby (ERB) as well as ES2015 template strings. Change the
     * following template settings to use alternative delimiters.
     *
     * @static
     * @memberOf _
     * @type {Object}
     */
    lodash.templateSettings = {

      /**
       * Used to detect `data` property values to be HTML-escaped.
       *
       * @memberOf _.templateSettings
       * @type {RegExp}
       */
      'escape': reEscape,

      /**
       * Used to detect code to be evaluated.
       *
       * @memberOf _.templateSettings
       * @type {RegExp}
       */
      'evaluate': reEvaluate,

      /**
       * Used to detect `data` property values to inject.
       *
       * @memberOf _.templateSettings
       * @type {RegExp}
       */
      'interpolate': reInterpolate,

      /**
       * Used to reference the data object in the template text.
       *
       * @memberOf _.templateSettings
       * @type {string}
       */
      'variable': '',

      /**
       * Used to import variables into the compiled template.
       *
       * @memberOf _.templateSettings
       * @type {Object}
       */
      'imports': {

        /**
         * A reference to the `lodash` function.
         *
         * @memberOf _.templateSettings.imports
         * @type {Function}
         */
        '_': lodash
      }
    };

    // Ensure wrappers are instances of `baseLodash`.
    lodash.prototype = baseLodash.prototype;
    lodash.prototype.constructor = lodash;

    LodashWrapper.prototype = baseCreate(baseLodash.prototype);
    LodashWrapper.prototype.constructor = LodashWrapper;

    /*------------------------------------------------------------------------*/

    /**
     * Creates a lazy wrapper object which wraps `value` to enable lazy evaluation.
     *
     * @private
     * @constructor
     * @param {*} value The value to wrap.
     */
    function LazyWrapper(value) {
      this.__wrapped__ = value;
      this.__actions__ = [];
      this.__dir__ = 1;
      this.__filtered__ = false;
      this.__iteratees__ = [];
      this.__takeCount__ = MAX_ARRAY_LENGTH;
      this.__views__ = [];
    }

    /**
     * Creates a clone of the lazy wrapper object.
     *
     * @private
     * @name clone
     * @memberOf LazyWrapper
     * @returns {Object} Returns the cloned `LazyWrapper` object.
     */
    function lazyClone() {
      var result = new LazyWrapper(this.__wrapped__);
      result.__actions__ = copyArray(this.__actions__);
      result.__dir__ = this.__dir__;
      result.__filtered__ = this.__filtered__;
      result.__iteratees__ = copyArray(this.__iteratees__);
      result.__takeCount__ = this.__takeCount__;
      result.__views__ = copyArray(this.__views__);
      return result;
    }

    /**
     * Reverses the direction of lazy iteration.
     *
     * @private
     * @name reverse
     * @memberOf LazyWrapper
     * @returns {Object} Returns the new reversed `LazyWrapper` object.
     */
    function lazyReverse() {
      if (this.__filtered__) {
        var result = new LazyWrapper(this);
        result.__dir__ = -1;
        result.__filtered__ = true;
      } else {
        result = this.clone();
        result.__dir__ *= -1;
      }
      return result;
    }

    /**
     * Extracts the unwrapped value from its lazy wrapper.
     *
     * @private
     * @name value
     * @memberOf LazyWrapper
     * @returns {*} Returns the unwrapped value.
     */
    function lazyValue() {
      var array = this.__wrapped__.value(),
          dir = this.__dir__,
          isArr = isArray(array),
          isRight = dir < 0,
          arrLength = isArr ? array.length : 0,
          view = getView(0, arrLength, this.__views__),
          start = view.start,
          end = view.end,
          length = end - start,
          index = isRight ? end : (start - 1),
          iteratees = this.__iteratees__,
          iterLength = iteratees.length,
          resIndex = 0,
          takeCount = nativeMin(length, this.__takeCount__);

      if (!isArr || (!isRight && arrLength == length && takeCount == length)) {
        return baseWrapperValue(array, this.__actions__);
      }
      var result = [];

      outer:
      while (length-- && resIndex < takeCount) {
        index += dir;

        var iterIndex = -1,
            value = array[index];

        while (++iterIndex < iterLength) {
          var data = iteratees[iterIndex],
              iteratee = data.iteratee,
              type = data.type,
              computed = iteratee(value);

          if (type == LAZY_MAP_FLAG) {
            value = computed;
          } else if (!computed) {
            if (type == LAZY_FILTER_FLAG) {
              continue outer;
            } else {
              break outer;
            }
          }
        }
        result[resIndex++] = value;
      }
      return result;
    }

    // Ensure `LazyWrapper` is an instance of `baseLodash`.
    LazyWrapper.prototype = baseCreate(baseLodash.prototype);
    LazyWrapper.prototype.constructor = LazyWrapper;

    /*------------------------------------------------------------------------*/

    /**
     * Creates a hash object.
     *
     * @private
     * @constructor
     * @param {Array} [entries] The key-value pairs to cache.
     */
    function Hash(entries) {
      var index = -1,
          length = entries == null ? 0 : entries.length;

      this.clear();
      while (++index < length) {
        var entry = entries[index];
        this.set(entry[0], entry[1]);
      }
    }

    /**
     * Removes all key-value entries from the hash.
     *
     * @private
     * @name clear
     * @memberOf Hash
     */
    function hashClear() {
      this.__data__ = nativeCreate ? nativeCreate(null) : {};
      this.size = 0;
    }

    /**
     * Removes `key` and its value from the hash.
     *
     * @private
     * @name delete
     * @memberOf Hash
     * @param {Object} hash The hash to modify.
     * @param {string} key The key of the value to remove.
     * @returns {boolean} Returns `true` if the entry was removed, else `false`.
     */
    function hashDelete(key) {
      var result = this.has(key) && delete this.__data__[key];
      this.size -= result ? 1 : 0;
      return result;
    }

    /**
     * Gets the hash value for `key`.
     *
     * @private
     * @name get
     * @memberOf Hash
     * @param {string} key The key of the value to get.
     * @returns {*} Returns the entry value.
     */
    function hashGet(key) {
      var data = this.__data__;
      if (nativeCreate) {
        var result = data[key];
        return result === HASH_UNDEFINED ? undefined : result;
      }
      return hasOwnProperty.call(data, key) ? data[key] : undefined;
    }

    /**
     * Checks if a hash value for `key` exists.
     *
     * @private
     * @name has
     * @memberOf Hash
     * @param {string} key The key of the entry to check.
     * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
     */
    function hashHas(key) {
      var data = this.__data__;
      return nativeCreate ? (data[key] !== undefined) : hasOwnProperty.call(data, key);
    }

    /**
     * Sets the hash `key` to `value`.
     *
     * @private
     * @name set
     * @memberOf Hash
     * @param {string} key The key of the value to set.
     * @param {*} value The value to set.
     * @returns {Object} Returns the hash instance.
     */
    function hashSet(key, value) {
      var data = this.__data__;
      this.size += this.has(key) ? 0 : 1;
      data[key] = (nativeCreate && value === undefined) ? HASH_UNDEFINED : value;
      return this;
    }

    // Add methods to `Hash`.
    Hash.prototype.clear = hashClear;
    Hash.prototype['delete'] = hashDelete;
    Hash.prototype.get = hashGet;
    Hash.prototype.has = hashHas;
    Hash.prototype.set = hashSet;

    /*------------------------------------------------------------------------*/

    /**
     * Creates an list cache object.
     *
     * @private
     * @constructor
     * @param {Array} [entries] The key-value pairs to cache.
     */
    function ListCache(entries) {
      var index = -1,
          length = entries == null ? 0 : entries.length;

      this.clear();
      while (++index < length) {
        var entry = entries[index];
        this.set(entry[0], entry[1]);
      }
    }

    /**
     * Removes all key-value entries from the list cache.
     *
     * @private
     * @name clear
     * @memberOf ListCache
     */
    function listCacheClear() {
      this.__data__ = [];
      this.size = 0;
    }

    /**
     * Removes `key` and its value from the list cache.
     *
     * @private
     * @name delete
     * @memberOf ListCache
     * @param {string} key The key of the value to remove.
     * @returns {boolean} Returns `true` if the entry was removed, else `false`.
     */
    function listCacheDelete(key) {
      var data = this.__data__,
          index = assocIndexOf(data, key);

      if (index < 0) {
        return false;
      }
      var lastIndex = data.length - 1;
      if (index == lastIndex) {
        data.pop();
      } else {
        splice.call(data, index, 1);
      }
      --this.size;
      return true;
    }

    /**
     * Gets the list cache value for `key`.
     *
     * @private
     * @name get
     * @memberOf ListCache
     * @param {string} key The key of the value to get.
     * @returns {*} Returns the entry value.
     */
    function listCacheGet(key) {
      var data = this.__data__,
          index = assocIndexOf(data, key);

      return index < 0 ? undefined : data[index][1];
    }

    /**
     * Checks if a list cache value for `key` exists.
     *
     * @private
     * @name has
     * @memberOf ListCache
     * @param {string} key The key of the entry to check.
     * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
     */
    function listCacheHas(key) {
      return assocIndexOf(this.__data__, key) > -1;
    }

    /**
     * Sets the list cache `key` to `value`.
     *
     * @private
     * @name set
     * @memberOf ListCache
     * @param {string} key The key of the value to set.
     * @param {*} value The value to set.
     * @returns {Object} Returns the list cache instance.
     */
    function listCacheSet(key, value) {
      var data = this.__data__,
          index = assocIndexOf(data, key);

      if (index < 0) {
        ++this.size;
        data.push([key, value]);
      } else {
        data[index][1] = value;
      }
      return this;
    }

    // Add methods to `ListCache`.
    ListCache.prototype.clear = listCacheClear;
    ListCache.prototype['delete'] = listCacheDelete;
    ListCache.prototype.get = listCacheGet;
    ListCache.prototype.has = listCacheHas;
    ListCache.prototype.set = listCacheSet;

    /*------------------------------------------------------------------------*/

    /**
     * Creates a map cache object to store key-value pairs.
     *
     * @private
     * @constructor
     * @param {Array} [entries] The key-value pairs to cache.
     */
    function MapCache(entries) {
      var index = -1,
          length = entries == null ? 0 : entries.length;

      this.clear();
      while (++index < length) {
        var entry = entries[index];
        this.set(entry[0], entry[1]);
      }
    }

    /**
     * Removes all key-value entries from the map.
     *
     * @private
     * @name clear
     * @memberOf MapCache
     */
    function mapCacheClear() {
      this.size = 0;
      this.__data__ = {
        'hash': new Hash,
        'map': new (Map || ListCache),
        'string': new Hash
      };
    }

    /**
     * Removes `key` and its value from the map.
     *
     * @private
     * @name delete
     * @memberOf MapCache
     * @param {string} key The key of the value to remove.
     * @returns {boolean} Returns `true` if the entry was removed, else `false`.
     */
    function mapCacheDelete(key) {
      var result = getMapData(this, key)['delete'](key);
      this.size -= result ? 1 : 0;
      return result;
    }

    /**
     * Gets the map value for `key`.
     *
     * @private
     * @name get
     * @memberOf MapCache
     * @param {string} key The key of the value to get.
     * @returns {*} Returns the entry value.
     */
    function mapCacheGet(key) {
      return getMapData(this, key).get(key);
    }

    /**
     * Checks if a map value for `key` exists.
     *
     * @private
     * @name has
     * @memberOf MapCache
     * @param {string} key The key of the entry to check.
     * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
     */
    function mapCacheHas(key) {
      return getMapData(this, key).has(key);
    }

    /**
     * Sets the map `key` to `value`.
     *
     * @private
     * @name set
     * @memberOf MapCache
     * @param {string} key The key of the value to set.
     * @param {*} value The value to set.
     * @returns {Object} Returns the map cache instance.
     */
    function mapCacheSet(key, value) {
      var data = getMapData(this, key),
          size = data.size;

      data.set(key, value);
      this.size += data.size == size ? 0 : 1;
      return this;
    }

    // Add methods to `MapCache`.
    MapCache.prototype.clear = mapCacheClear;
    MapCache.prototype['delete'] = mapCacheDelete;
    MapCache.prototype.get = mapCacheGet;
    MapCache.prototype.has = mapCacheHas;
    MapCache.prototype.set = mapCacheSet;

    /*------------------------------------------------------------------------*/

    /**
     *
     * Creates an array cache object to store unique values.
     *
     * @private
     * @constructor
     * @param {Array} [values] The values to cache.
     */
    function SetCache(values) {
      var index = -1,
          length = values == null ? 0 : values.length;

      this.__data__ = new MapCache;
      while (++index < length) {
        this.add(values[index]);
      }
    }

    /**
     * Adds `value` to the array cache.
     *
     * @private
     * @name add
     * @memberOf SetCache
     * @alias push
     * @param {*} value The value to cache.
     * @returns {Object} Returns the cache instance.
     */
    function setCacheAdd(value) {
      this.__data__.set(value, HASH_UNDEFINED);
      return this;
    }

    /**
     * Checks if `value` is in the array cache.
     *
     * @private
     * @name has
     * @memberOf SetCache
     * @param {*} value The value to search for.
     * @returns {number} Returns `true` if `value` is found, else `false`.
     */
    function setCacheHas(value) {
      return this.__data__.has(value);
    }

    // Add methods to `SetCache`.
    SetCache.prototype.add = SetCache.prototype.push = setCacheAdd;
    SetCache.prototype.has = setCacheHas;

    /*------------------------------------------------------------------------*/

    /**
     * Creates a stack cache object to store key-value pairs.
     *
     * @private
     * @constructor
     * @param {Array} [entries] The key-value pairs to cache.
     */
    function Stack(entries) {
      var data = this.__data__ = new ListCache(entries);
      this.size = data.size;
    }

    /**
     * Removes all key-value entries from the stack.
     *
     * @private
     * @name clear
     * @memberOf Stack
     */
    function stackClear() {
      this.__data__ = new ListCache;
      this.size = 0;
    }

    /**
     * Removes `key` and its value from the stack.
     *
     * @private
     * @name delete
     * @memberOf Stack
     * @param {string} key The key of the value to remove.
     * @returns {boolean} Returns `true` if the entry was removed, else `false`.
     */
    function stackDelete(key) {
      var data = this.__data__,
          result = data['delete'](key);

      this.size = data.size;
      return result;
    }

    /**
     * Gets the stack value for `key`.
     *
     * @private
     * @name get
     * @memberOf Stack
     * @param {string} key The key of the value to get.
     * @returns {*} Returns the entry value.
     */
    function stackGet(key) {
      return this.__data__.get(key);
    }

    /**
     * Checks if a stack value for `key` exists.
     *
     * @private
     * @name has
     * @memberOf Stack
     * @param {string} key The key of the entry to check.
     * @returns {boolean} Returns `true` if an entry for `key` exists, else `false`.
     */
    function stackHas(key) {
      return this.__data__.has(key);
    }

    /**
     * Sets the stack `key` to `value`.
     *
     * @private
     * @name set
     * @memberOf Stack
     * @param {string} key The key of the value to set.
     * @param {*} value The value to set.
     * @returns {Object} Returns the stack cache instance.
     */
    function stackSet(key, value) {
      var data = this.__data__;
      if (data instanceof ListCache) {
        var pairs = data.__data__;
        if (!Map || (pairs.length < LARGE_ARRAY_SIZE - 1)) {
          pairs.push([key, value]);
          this.size = ++data.size;
          return this;
        }
        data = this.__data__ = new MapCache(pairs);
      }
      data.set(key, value);
      this.size = data.size;
      return this;
    }

    // Add methods to `Stack`.
    Stack.prototype.clear = stackClear;
    Stack.prototype['delete'] = stackDelete;
    Stack.prototype.get = stackGet;
    Stack.prototype.has = stackHas;
    Stack.prototype.set = stackSet;

    /*------------------------------------------------------------------------*/

    /**
     * Creates an array of the enumerable property names of the array-like `value`.
     *
     * @private
     * @param {*} value The value to query.
     * @param {boolean} inherited Specify returning inherited property names.
     * @returns {Array} Returns the array of property names.
     */
    function arrayLikeKeys(value, inherited) {
      var isArr = isArray(value),
          isArg = !isArr && isArguments(value),
          isBuff = !isArr && !isArg && isBuffer(value),
          isType = !isArr && !isArg && !isBuff && isTypedArray(value),
          skipIndexes = isArr || isArg || isBuff || isType,
          result = skipIndexes ? baseTimes(value.length, String) : [],
          length = result.length;

      for (var key in value) {
        if ((inherited || hasOwnProperty.call(value, key)) &&
            !(skipIndexes && (
               // Safari 9 has enumerable `arguments.length` in strict mode.
               key == 'length' ||
               // Node.js 0.10 has enumerable non-index properties on buffers.
               (isBuff && (key == 'offset' || key == 'parent')) ||
               // PhantomJS 2 has enumerable non-index properties on typed arrays.
               (isType && (key == 'buffer' || key == 'byteLength' || key == 'byteOffset')) ||
               // Skip index properties.
               isIndex(key, length)
            ))) {
          result.push(key);
        }
      }
      return result;
    }

    /**
     * A specialized version of `_.sample` for arrays.
     *
     * @private
     * @param {Array} array The array to sample.
     * @returns {*} Returns the random element.
     */
    function arraySample(array) {
      var length = array.length;
      return length ? array[baseRandom(0, length - 1)] : undefined;
    }

    /**
     * A specialized version of `_.sampleSize` for arrays.
     *
     * @private
     * @param {Array} array The array to sample.
     * @param {number} n The number of elements to sample.
     * @returns {Array} Returns the random elements.
     */
    function arraySampleSize(array, n) {
      return shuffleSelf(copyArray(array), baseClamp(n, 0, array.length));
    }

    /**
     * A specialized version of `_.shuffle` for arrays.
     *
     * @private
     * @param {Array} array The array to shuffle.
     * @returns {Array} Returns the new shuffled array.
     */
    function arrayShuffle(array) {
      return shuffleSelf(copyArray(array));
    }

    /**
     * This function is like `assignValue` except that it doesn't assign
     * `undefined` values.
     *
     * @private
     * @param {Object} object The object to modify.
     * @param {string} key The key of the property to assign.
     * @param {*} value The value to assign.
     */
    function assignMergeValue(object, key, value) {
      if ((value !== undefined && !eq(object[key], value)) ||
          (value === undefined && !(key in object))) {
        baseAssignValue(object, key, value);
      }
    }

    /**
     * Assigns `value` to `key` of `object` if the existing value is not equivalent
     * using [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
     * for equality comparisons.
     *
     * @private
     * @param {Object} object The object to modify.
     * @param {string} key The key of the property to assign.
     * @param {*} value The value to assign.
     */
    function assignValue(object, key, value) {
      var objValue = object[key];
      if (!(hasOwnProperty.call(object, key) && eq(objValue, value)) ||
          (value === undefined && !(key in object))) {
        baseAssignValue(object, key, value);
      }
    }

    /**
     * Gets the index at which the `key` is found in `array` of key-value pairs.
     *
     * @private
     * @param {Array} array The array to inspect.
     * @param {*} key The key to search for.
     * @returns {number} Returns the index of the matched value, else `-1`.
     */
    function assocIndexOf(array, key) {
      var length = array.length;
      while (length--) {
        if (eq(array[length][0], key)) {
          return length;
        }
      }
      return -1;
    }

    /**
     * Aggregates elements of `collection` on `accumulator` with keys transformed
     * by `iteratee` and values set by `setter`.
     *
     * @private
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} setter The function to set `accumulator` values.
     * @param {Function} iteratee The iteratee to transform keys.
     * @param {Object} accumulator The initial aggregated object.
     * @returns {Function} Returns `accumulator`.
     */
    function baseAggregator(collection, setter, iteratee, accumulator) {
      baseEach(collection, function(value, key, collection) {
        setter(accumulator, value, iteratee(value), collection);
      });
      return accumulator;
    }

    /**
     * The base implementation of `_.assign` without support for multiple sources
     * or `customizer` functions.
     *
     * @private
     * @param {Object} object The destination object.
     * @param {Object} source The source object.
     * @returns {Object} Returns `object`.
     */
    function baseAssign(object, source) {
      return object && copyObject(source, keys(source), object);
    }

    /**
     * The base implementation of `_.assignIn` without support for multiple sources
     * or `customizer` functions.
     *
     * @private
     * @param {Object} object The destination object.
     * @param {Object} source The source object.
     * @returns {Object} Returns `object`.
     */
    function baseAssignIn(object, source) {
      return object && copyObject(source, keysIn(source), object);
    }

    /**
     * The base implementation of `assignValue` and `assignMergeValue` without
     * value checks.
     *
     * @private
     * @param {Object} object The object to modify.
     * @param {string} key The key of the property to assign.
     * @param {*} value The value to assign.
     */
    function baseAssignValue(object, key, value) {
      if (key == '__proto__' && defineProperty) {
        defineProperty(object, key, {
          'configurable': true,
          'enumerable': true,
          'value': value,
          'writable': true
        });
      } else {
        object[key] = value;
      }
    }

    /**
     * The base implementation of `_.at` without support for individual paths.
     *
     * @private
     * @param {Object} object The object to iterate over.
     * @param {string[]} paths The property paths to pick.
     * @returns {Array} Returns the picked elements.
     */
    function baseAt(object, paths) {
      var index = -1,
          length = paths.length,
          result = Array(length),
          skip = object == null;

      while (++index < length) {
        result[index] = skip ? undefined : get(object, paths[index]);
      }
      return result;
    }

    /**
     * The base implementation of `_.clamp` which doesn't coerce arguments.
     *
     * @private
     * @param {number} number The number to clamp.
     * @param {number} [lower] The lower bound.
     * @param {number} upper The upper bound.
     * @returns {number} Returns the clamped number.
     */
    function baseClamp(number, lower, upper) {
      if (number === number) {
        if (upper !== undefined) {
          number = number <= upper ? number : upper;
        }
        if (lower !== undefined) {
          number = number >= lower ? number : lower;
        }
      }
      return number;
    }

    /**
     * The base implementation of `_.clone` and `_.cloneDeep` which tracks
     * traversed objects.
     *
     * @private
     * @param {*} value The value to clone.
     * @param {boolean} bitmask The bitmask flags.
     *  1 - Deep clone
     *  2 - Flatten inherited properties
     *  4 - Clone symbols
     * @param {Function} [customizer] The function to customize cloning.
     * @param {string} [key] The key of `value`.
     * @param {Object} [object] The parent object of `value`.
     * @param {Object} [stack] Tracks traversed objects and their clone counterparts.
     * @returns {*} Returns the cloned value.
     */
    function baseClone(value, bitmask, customizer, key, object, stack) {
      var result,
          isDeep = bitmask & CLONE_DEEP_FLAG,
          isFlat = bitmask & CLONE_FLAT_FLAG,
          isFull = bitmask & CLONE_SYMBOLS_FLAG;

      if (customizer) {
        result = object ? customizer(value, key, object, stack) : customizer(value);
      }
      if (result !== undefined) {
        return result;
      }
      if (!isObject(value)) {
        return value;
      }
      var isArr = isArray(value);
      if (isArr) {
        result = initCloneArray(value);
        if (!isDeep) {
          return copyArray(value, result);
        }
      } else {
        var tag = getTag(value),
            isFunc = tag == funcTag || tag == genTag;

        if (isBuffer(value)) {
          return cloneBuffer(value, isDeep);
        }
        if (tag == objectTag || tag == argsTag || (isFunc && !object)) {
          result = (isFlat || isFunc) ? {} : initCloneObject(value);
          if (!isDeep) {
            return isFlat
              ? copySymbolsIn(value, baseAssignIn(result, value))
              : copySymbols(value, baseAssign(result, value));
          }
        } else {
          if (!cloneableTags[tag]) {
            return object ? value : {};
          }
          result = initCloneByTag(value, tag, isDeep);
        }
      }
      // Check for circular references and return its corresponding clone.
      stack || (stack = new Stack);
      var stacked = stack.get(value);
      if (stacked) {
        return stacked;
      }
      stack.set(value, result);

      if (isSet(value)) {
        value.forEach(function(subValue) {
          result.add(baseClone(subValue, bitmask, customizer, subValue, value, stack));
        });
      } else if (isMap(value)) {
        value.forEach(function(subValue, key) {
          result.set(key, baseClone(subValue, bitmask, customizer, key, value, stack));
        });
      }

      var keysFunc = isFull
        ? (isFlat ? getAllKeysIn : getAllKeys)
        : (isFlat ? keysIn : keys);

      var props = isArr ? undefined : keysFunc(value);
      arrayEach(props || value, function(subValue, key) {
        if (props) {
          key = subValue;
          subValue = value[key];
        }
        // Recursively populate clone (susceptible to call stack limits).
        assignValue(result, key, baseClone(subValue, bitmask, customizer, key, value, stack));
      });
      return result;
    }

    /**
     * The base implementation of `_.conforms` which doesn't clone `source`.
     *
     * @private
     * @param {Object} source The object of property predicates to conform to.
     * @returns {Function} Returns the new spec function.
     */
    function baseConforms(source) {
      var props = keys(source);
      return function(object) {
        return baseConformsTo(object, source, props);
      };
    }

    /**
     * The base implementation of `_.conformsTo` which accepts `props` to check.
     *
     * @private
     * @param {Object} object The object to inspect.
     * @param {Object} source The object of property predicates to conform to.
     * @returns {boolean} Returns `true` if `object` conforms, else `false`.
     */
    function baseConformsTo(object, source, props) {
      var length = props.length;
      if (object == null) {
        return !length;
      }
      object = Object(object);
      while (length--) {
        var key = props[length],
            predicate = source[key],
            value = object[key];

        if ((value === undefined && !(key in object)) || !predicate(value)) {
          return false;
        }
      }
      return true;
    }

    /**
     * The base implementation of `_.delay` and `_.defer` which accepts `args`
     * to provide to `func`.
     *
     * @private
     * @param {Function} func The function to delay.
     * @param {number} wait The number of milliseconds to delay invocation.
     * @param {Array} args The arguments to provide to `func`.
     * @returns {number|Object} Returns the timer id or timeout object.
     */
    function baseDelay(func, wait, args) {
      if (typeof func != 'function') {
        throw new TypeError(FUNC_ERROR_TEXT);
      }
      return setTimeout(function() { func.apply(undefined, args); }, wait);
    }

    /**
     * The base implementation of methods like `_.difference` without support
     * for excluding multiple arrays or iteratee shorthands.
     *
     * @private
     * @param {Array} array The array to inspect.
     * @param {Array} values The values to exclude.
     * @param {Function} [iteratee] The iteratee invoked per element.
     * @param {Function} [comparator] The comparator invoked per element.
     * @returns {Array} Returns the new array of filtered values.
     */
    function baseDifference(array, values, iteratee, comparator) {
      var index = -1,
          includes = arrayIncludes,
          isCommon = true,
          length = array.length,
          result = [],
          valuesLength = values.length;

      if (!length) {
        return result;
      }
      if (iteratee) {
        values = arrayMap(values, baseUnary(iteratee));
      }
      if (comparator) {
        includes = arrayIncludesWith;
        isCommon = false;
      }
      else if (values.length >= LARGE_ARRAY_SIZE) {
        includes = cacheHas;
        isCommon = false;
        values = new SetCache(values);
      }
      outer:
      while (++index < length) {
        var value = array[index],
            computed = iteratee == null ? value : iteratee(value);

        value = (comparator || value !== 0) ? value : 0;
        if (isCommon && computed === computed) {
          var valuesIndex = valuesLength;
          while (valuesIndex--) {
            if (values[valuesIndex] === computed) {
              continue outer;
            }
          }
          result.push(value);
        }
        else if (!includes(values, computed, comparator)) {
          result.push(value);
        }
      }
      return result;
    }

    /**
     * The base implementation of `_.forEach` without support for iteratee shorthands.
     *
     * @private
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} iteratee The function invoked per iteration.
     * @returns {Array|Object} Returns `collection`.
     */
    var baseEach = createBaseEach(baseForOwn);

    /**
     * The base implementation of `_.forEachRight` without support for iteratee shorthands.
     *
     * @private
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} iteratee The function invoked per iteration.
     * @returns {Array|Object} Returns `collection`.
     */
    var baseEachRight = createBaseEach(baseForOwnRight, true);

    /**
     * The base implementation of `_.every` without support for iteratee shorthands.
     *
     * @private
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} predicate The function invoked per iteration.
     * @returns {boolean} Returns `true` if all elements pass the predicate check,
     *  else `false`
     */
    function baseEvery(collection, predicate) {
      var result = true;
      baseEach(collection, function(value, index, collection) {
        result = !!predicate(value, index, collection);
        return result;
      });
      return result;
    }

    /**
     * The base implementation of methods like `_.max` and `_.min` which accepts a
     * `comparator` to determine the extremum value.
     *
     * @private
     * @param {Array} array The array to iterate over.
     * @param {Function} iteratee The iteratee invoked per iteration.
     * @param {Function} comparator The comparator used to compare values.
     * @returns {*} Returns the extremum value.
     */
    function baseExtremum(array, iteratee, comparator) {
      var index = -1,
          length = array.length;

      while (++index < length) {
        var value = array[index],
            current = iteratee(value);

        if (current != null && (computed === undefined
              ? (current === current && !isSymbol(current))
              : comparator(current, computed)
            )) {
          var computed = current,
              result = value;
        }
      }
      return result;
    }

    /**
     * The base implementation of `_.fill` without an iteratee call guard.
     *
     * @private
     * @param {Array} array The array to fill.
     * @param {*} value The value to fill `array` with.
     * @param {number} [start=0] The start position.
     * @param {number} [end=array.length] The end position.
     * @returns {Array} Returns `array`.
     */
    function baseFill(array, value, start, end) {
      var length = array.length;

      start = toInteger(start);
      if (start < 0) {
        start = -start > length ? 0 : (length + start);
      }
      end = (end === undefined || end > length) ? length : toInteger(end);
      if (end < 0) {
        end += length;
      }
      end = start > end ? 0 : toLength(end);
      while (start < end) {
        array[start++] = value;
      }
      return array;
    }

    /**
     * The base implementation of `_.filter` without support for iteratee shorthands.
     *
     * @private
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} predicate The function invoked per iteration.
     * @returns {Array} Returns the new filtered array.
     */
    function baseFilter(collection, predicate) {
      var result = [];
      baseEach(collection, function(value, index, collection) {
        if (predicate(value, index, collection)) {
          result.push(value);
        }
      });
      return result;
    }

    /**
     * The base implementation of `_.flatten` with support for restricting flattening.
     *
     * @private
     * @param {Array} array The array to flatten.
     * @param {number} depth The maximum recursion depth.
     * @param {boolean} [predicate=isFlattenable] The function invoked per iteration.
     * @param {boolean} [isStrict] Restrict to values that pass `predicate` checks.
     * @param {Array} [result=[]] The initial result value.
     * @returns {Array} Returns the new flattened array.
     */
    function baseFlatten(array, depth, predicate, isStrict, result) {
      var index = -1,
          length = array.length;

      predicate || (predicate = isFlattenable);
      result || (result = []);

      while (++index < length) {
        var value = array[index];
        if (depth > 0 && predicate(value)) {
          if (depth > 1) {
            // Recursively flatten arrays (susceptible to call stack limits).
            baseFlatten(value, depth - 1, predicate, isStrict, result);
          } else {
            arrayPush(result, value);
          }
        } else if (!isStrict) {
          result[result.length] = value;
        }
      }
      return result;
    }

    /**
     * The base implementation of `baseForOwn` which iterates over `object`
     * properties returned by `keysFunc` and invokes `iteratee` for each property.
     * Iteratee functions may exit iteration early by explicitly returning `false`.
     *
     * @private
     * @param {Object} object The object to iterate over.
     * @param {Function} iteratee The function invoked per iteration.
     * @param {Function} keysFunc The function to get the keys of `object`.
     * @returns {Object} Returns `object`.
     */
    var baseFor = createBaseFor();

    /**
     * This function is like `baseFor` except that it iterates over properties
     * in the opposite order.
     *
     * @private
     * @param {Object} object The object to iterate over.
     * @param {Function} iteratee The function invoked per iteration.
     * @param {Function} keysFunc The function to get the keys of `object`.
     * @returns {Object} Returns `object`.
     */
    var baseForRight = createBaseFor(true);

    /**
     * The base implementation of `_.forOwn` without support for iteratee shorthands.
     *
     * @private
     * @param {Object} object The object to iterate over.
     * @param {Function} iteratee The function invoked per iteration.
     * @returns {Object} Returns `object`.
     */
    function baseForOwn(object, iteratee) {
      return object && baseFor(object, iteratee, keys);
    }

    /**
     * The base implementation of `_.forOwnRight` without support for iteratee shorthands.
     *
     * @private
     * @param {Object} object The object to iterate over.
     * @param {Function} iteratee The function invoked per iteration.
     * @returns {Object} Returns `object`.
     */
    function baseForOwnRight(object, iteratee) {
      return object && baseForRight(object, iteratee, keys);
    }

    /**
     * The base implementation of `_.functions` which creates an array of
     * `object` function property names filtered from `props`.
     *
     * @private
     * @param {Object} object The object to inspect.
     * @param {Array} props The property names to filter.
     * @returns {Array} Returns the function names.
     */
    function baseFunctions(object, props) {
      return arrayFilter(props, function(key) {
        return isFunction(object[key]);
      });
    }

    /**
     * The base implementation of `_.get` without support for default values.
     *
     * @private
     * @param {Object} object The object to query.
     * @param {Array|string} path The path of the property to get.
     * @returns {*} Returns the resolved value.
     */
    function baseGet(object, path) {
      path = castPath(path, object);

      var index = 0,
          length = path.length;

      while (object != null && index < length) {
        object = object[toKey(path[index++])];
      }
      return (index && index == length) ? object : undefined;
    }

    /**
     * The base implementation of `getAllKeys` and `getAllKeysIn` which uses
     * `keysFunc` and `symbolsFunc` to get the enumerable property names and
     * symbols of `object`.
     *
     * @private
     * @param {Object} object The object to query.
     * @param {Function} keysFunc The function to get the keys of `object`.
     * @param {Function} symbolsFunc The function to get the symbols of `object`.
     * @returns {Array} Returns the array of property names and symbols.
     */
    function baseGetAllKeys(object, keysFunc, symbolsFunc) {
      var result = keysFunc(object);
      return isArray(object) ? result : arrayPush(result, symbolsFunc(object));
    }

    /**
     * The base implementation of `getTag` without fallbacks for buggy environments.
     *
     * @private
     * @param {*} value The value to query.
     * @returns {string} Returns the `toStringTag`.
     */
    function baseGetTag(value) {
      if (value == null) {
        return value === undefined ? undefinedTag : nullTag;
      }
      return (symToStringTag && symToStringTag in Object(value))
        ? getRawTag(value)
        : objectToString(value);
    }

    /**
     * The base implementation of `_.gt` which doesn't coerce arguments.
     *
     * @private
     * @param {*} value The value to compare.
     * @param {*} other The other value to compare.
     * @returns {boolean} Returns `true` if `value` is greater than `other`,
     *  else `false`.
     */
    function baseGt(value, other) {
      return value > other;
    }

    /**
     * The base implementation of `_.has` without support for deep paths.
     *
     * @private
     * @param {Object} [object] The object to query.
     * @param {Array|string} key The key to check.
     * @returns {boolean} Returns `true` if `key` exists, else `false`.
     */
    function baseHas(object, key) {
      return object != null && hasOwnProperty.call(object, key);
    }

    /**
     * The base implementation of `_.hasIn` without support for deep paths.
     *
     * @private
     * @param {Object} [object] The object to query.
     * @param {Array|string} key The key to check.
     * @returns {boolean} Returns `true` if `key` exists, else `false`.
     */
    function baseHasIn(object, key) {
      return object != null && key in Object(object);
    }

    /**
     * The base implementation of `_.inRange` which doesn't coerce arguments.
     *
     * @private
     * @param {number} number The number to check.
     * @param {number} start The start of the range.
     * @param {number} end The end of the range.
     * @returns {boolean} Returns `true` if `number` is in the range, else `false`.
     */
    function baseInRange(number, start, end) {
      return number >= nativeMin(start, end) && number < nativeMax(start, end);
    }

    /**
     * The base implementation of methods like `_.intersection`, without support
     * for iteratee shorthands, that accepts an array of arrays to inspect.
     *
     * @private
     * @param {Array} arrays The arrays to inspect.
     * @param {Function} [iteratee] The iteratee invoked per element.
     * @param {Function} [comparator] The comparator invoked per element.
     * @returns {Array} Returns the new array of shared values.
     */
    function baseIntersection(arrays, iteratee, comparator) {
      var includes = comparator ? arrayIncludesWith : arrayIncludes,
          length = arrays[0].length,
          othLength = arrays.length,
          othIndex = othLength,
          caches = Array(othLength),
          maxLength = Infinity,
          result = [];

      while (othIndex--) {
        var array = arrays[othIndex];
        if (othIndex && iteratee) {
          array = arrayMap(array, baseUnary(iteratee));
        }
        maxLength = nativeMin(array.length, maxLength);
        caches[othIndex] = !comparator && (iteratee || (length >= 120 && array.length >= 120))
          ? new SetCache(othIndex && array)
          : undefined;
      }
      array = arrays[0];

      var index = -1,
          seen = caches[0];

      outer:
      while (++index < length && result.length < maxLength) {
        var value = array[index],
            computed = iteratee ? iteratee(value) : value;

        value = (comparator || value !== 0) ? value : 0;
        if (!(seen
              ? cacheHas(seen, computed)
              : includes(result, computed, comparator)
            )) {
          othIndex = othLength;
          while (--othIndex) {
            var cache = caches[othIndex];
            if (!(cache
                  ? cacheHas(cache, computed)
                  : includes(arrays[othIndex], computed, comparator))
                ) {
              continue outer;
            }
          }
          if (seen) {
            seen.push(computed);
          }
          result.push(value);
        }
      }
      return result;
    }

    /**
     * The base implementation of `_.invert` and `_.invertBy` which inverts
     * `object` with values transformed by `iteratee` and set by `setter`.
     *
     * @private
     * @param {Object} object The object to iterate over.
     * @param {Function} setter The function to set `accumulator` values.
     * @param {Function} iteratee The iteratee to transform values.
     * @param {Object} accumulator The initial inverted object.
     * @returns {Function} Returns `accumulator`.
     */
    function baseInverter(object, setter, iteratee, accumulator) {
      baseForOwn(object, function(value, key, object) {
        setter(accumulator, iteratee(value), key, object);
      });
      return accumulator;
    }

    /**
     * The base implementation of `_.invoke` without support for individual
     * method arguments.
     *
     * @private
     * @param {Object} object The object to query.
     * @param {Array|string} path The path of the method to invoke.
     * @param {Array} args The arguments to invoke the method with.
     * @returns {*} Returns the result of the invoked method.
     */
    function baseInvoke(object, path, args) {
      path = castPath(path, object);
      object = parent(object, path);
      var func = object == null ? object : object[toKey(last(path))];
      return func == null ? undefined : apply(func, object, args);
    }

    /**
     * The base implementation of `_.isArguments`.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is an `arguments` object,
     */
    function baseIsArguments(value) {
      return isObjectLike(value) && baseGetTag(value) == argsTag;
    }

    /**
     * The base implementation of `_.isArrayBuffer` without Node.js optimizations.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is an array buffer, else `false`.
     */
    function baseIsArrayBuffer(value) {
      return isObjectLike(value) && baseGetTag(value) == arrayBufferTag;
    }

    /**
     * The base implementation of `_.isDate` without Node.js optimizations.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a date object, else `false`.
     */
    function baseIsDate(value) {
      return isObjectLike(value) && baseGetTag(value) == dateTag;
    }

    /**
     * The base implementation of `_.isEqual` which supports partial comparisons
     * and tracks traversed objects.
     *
     * @private
     * @param {*} value The value to compare.
     * @param {*} other The other value to compare.
     * @param {boolean} bitmask The bitmask flags.
     *  1 - Unordered comparison
     *  2 - Partial comparison
     * @param {Function} [customizer] The function to customize comparisons.
     * @param {Object} [stack] Tracks traversed `value` and `other` objects.
     * @returns {boolean} Returns `true` if the values are equivalent, else `false`.
     */
    function baseIsEqual(value, other, bitmask, customizer, stack) {
      if (value === other) {
        return true;
      }
      if (value == null || other == null || (!isObjectLike(value) && !isObjectLike(other))) {
        return value !== value && other !== other;
      }
      return baseIsEqualDeep(value, other, bitmask, customizer, baseIsEqual, stack);
    }

    /**
     * A specialized version of `baseIsEqual` for arrays and objects which performs
     * deep comparisons and tracks traversed objects enabling objects with circular
     * references to be compared.
     *
     * @private
     * @param {Object} object The object to compare.
     * @param {Object} other The other object to compare.
     * @param {number} bitmask The bitmask flags. See `baseIsEqual` for more details.
     * @param {Function} customizer The function to customize comparisons.
     * @param {Function} equalFunc The function to determine equivalents of values.
     * @param {Object} [stack] Tracks traversed `object` and `other` objects.
     * @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
     */
    function baseIsEqualDeep(object, other, bitmask, customizer, equalFunc, stack) {
      var objIsArr = isArray(object),
          othIsArr = isArray(other),
          objTag = objIsArr ? arrayTag : getTag(object),
          othTag = othIsArr ? arrayTag : getTag(other);

      objTag = objTag == argsTag ? objectTag : objTag;
      othTag = othTag == argsTag ? objectTag : othTag;

      var objIsObj = objTag == objectTag,
          othIsObj = othTag == objectTag,
          isSameTag = objTag == othTag;

      if (isSameTag && isBuffer(object)) {
        if (!isBuffer(other)) {
          return false;
        }
        objIsArr = true;
        objIsObj = false;
      }
      if (isSameTag && !objIsObj) {
        stack || (stack = new Stack);
        return (objIsArr || isTypedArray(object))
          ? equalArrays(object, other, bitmask, customizer, equalFunc, stack)
          : equalByTag(object, other, objTag, bitmask, customizer, equalFunc, stack);
      }
      if (!(bitmask & COMPARE_PARTIAL_FLAG)) {
        var objIsWrapped = objIsObj && hasOwnProperty.call(object, '__wrapped__'),
            othIsWrapped = othIsObj && hasOwnProperty.call(other, '__wrapped__');

        if (objIsWrapped || othIsWrapped) {
          var objUnwrapped = objIsWrapped ? object.value() : object,
              othUnwrapped = othIsWrapped ? other.value() : other;

          stack || (stack = new Stack);
          return equalFunc(objUnwrapped, othUnwrapped, bitmask, customizer, stack);
        }
      }
      if (!isSameTag) {
        return false;
      }
      stack || (stack = new Stack);
      return equalObjects(object, other, bitmask, customizer, equalFunc, stack);
    }

    /**
     * The base implementation of `_.isMap` without Node.js optimizations.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a map, else `false`.
     */
    function baseIsMap(value) {
      return isObjectLike(value) && getTag(value) == mapTag;
    }

    /**
     * The base implementation of `_.isMatch` without support for iteratee shorthands.
     *
     * @private
     * @param {Object} object The object to inspect.
     * @param {Object} source The object of property values to match.
     * @param {Array} matchData The property names, values, and compare flags to match.
     * @param {Function} [customizer] The function to customize comparisons.
     * @returns {boolean} Returns `true` if `object` is a match, else `false`.
     */
    function baseIsMatch(object, source, matchData, customizer) {
      var index = matchData.length,
          length = index,
          noCustomizer = !customizer;

      if (object == null) {
        return !length;
      }
      object = Object(object);
      while (index--) {
        var data = matchData[index];
        if ((noCustomizer && data[2])
              ? data[1] !== object[data[0]]
              : !(data[0] in object)
            ) {
          return false;
        }
      }
      while (++index < length) {
        data = matchData[index];
        var key = data[0],
            objValue = object[key],
            srcValue = data[1];

        if (noCustomizer && data[2]) {
          if (objValue === undefined && !(key in object)) {
            return false;
          }
        } else {
          var stack = new Stack;
          if (customizer) {
            var result = customizer(objValue, srcValue, key, object, source, stack);
          }
          if (!(result === undefined
                ? baseIsEqual(srcValue, objValue, COMPARE_PARTIAL_FLAG | COMPARE_UNORDERED_FLAG, customizer, stack)
                : result
              )) {
            return false;
          }
        }
      }
      return true;
    }

    /**
     * The base implementation of `_.isNative` without bad shim checks.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a native function,
     *  else `false`.
     */
    function baseIsNative(value) {
      if (!isObject(value) || isMasked(value)) {
        return false;
      }
      var pattern = isFunction(value) ? reIsNative : reIsHostCtor;
      return pattern.test(toSource(value));
    }

    /**
     * The base implementation of `_.isRegExp` without Node.js optimizations.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a regexp, else `false`.
     */
    function baseIsRegExp(value) {
      return isObjectLike(value) && baseGetTag(value) == regexpTag;
    }

    /**
     * The base implementation of `_.isSet` without Node.js optimizations.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a set, else `false`.
     */
    function baseIsSet(value) {
      return isObjectLike(value) && getTag(value) == setTag;
    }

    /**
     * The base implementation of `_.isTypedArray` without Node.js optimizations.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a typed array, else `false`.
     */
    function baseIsTypedArray(value) {
      return isObjectLike(value) &&
        isLength(value.length) && !!typedArrayTags[baseGetTag(value)];
    }

    /**
     * The base implementation of `_.iteratee`.
     *
     * @private
     * @param {*} [value=_.identity] The value to convert to an iteratee.
     * @returns {Function} Returns the iteratee.
     */
    function baseIteratee(value) {
      // Don't store the `typeof` result in a variable to avoid a JIT bug in Safari 9.
      // See https://bugs.webkit.org/show_bug.cgi?id=156034 for more details.
      if (typeof value == 'function') {
        return value;
      }
      if (value == null) {
        return identity;
      }
      if (typeof value == 'object') {
        return isArray(value)
          ? baseMatchesProperty(value[0], value[1])
          : baseMatches(value);
      }
      return property(value);
    }

    /**
     * The base implementation of `_.keys` which doesn't treat sparse arrays as dense.
     *
     * @private
     * @param {Object} object The object to query.
     * @returns {Array} Returns the array of property names.
     */
    function baseKeys(object) {
      if (!isPrototype(object)) {
        return nativeKeys(object);
      }
      var result = [];
      for (var key in Object(object)) {
        if (hasOwnProperty.call(object, key) && key != 'constructor') {
          result.push(key);
        }
      }
      return result;
    }

    /**
     * The base implementation of `_.keysIn` which doesn't treat sparse arrays as dense.
     *
     * @private
     * @param {Object} object The object to query.
     * @returns {Array} Returns the array of property names.
     */
    function baseKeysIn(object) {
      if (!isObject(object)) {
        return nativeKeysIn(object);
      }
      var isProto = isPrototype(object),
          result = [];

      for (var key in object) {
        if (!(key == 'constructor' && (isProto || !hasOwnProperty.call(object, key)))) {
          result.push(key);
        }
      }
      return result;
    }

    /**
     * The base implementation of `_.lt` which doesn't coerce arguments.
     *
     * @private
     * @param {*} value The value to compare.
     * @param {*} other The other value to compare.
     * @returns {boolean} Returns `true` if `value` is less than `other`,
     *  else `false`.
     */
    function baseLt(value, other) {
      return value < other;
    }

    /**
     * The base implementation of `_.map` without support for iteratee shorthands.
     *
     * @private
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} iteratee The function invoked per iteration.
     * @returns {Array} Returns the new mapped array.
     */
    function baseMap(collection, iteratee) {
      var index = -1,
          result = isArrayLike(collection) ? Array(collection.length) : [];

      baseEach(collection, function(value, key, collection) {
        result[++index] = iteratee(value, key, collection);
      });
      return result;
    }

    /**
     * The base implementation of `_.matches` which doesn't clone `source`.
     *
     * @private
     * @param {Object} source The object of property values to match.
     * @returns {Function} Returns the new spec function.
     */
    function baseMatches(source) {
      var matchData = getMatchData(source);
      if (matchData.length == 1 && matchData[0][2]) {
        return matchesStrictComparable(matchData[0][0], matchData[0][1]);
      }
      return function(object) {
        return object === source || baseIsMatch(object, source, matchData);
      };
    }

    /**
     * The base implementation of `_.matchesProperty` which doesn't clone `srcValue`.
     *
     * @private
     * @param {string} path The path of the property to get.
     * @param {*} srcValue The value to match.
     * @returns {Function} Returns the new spec function.
     */
    function baseMatchesProperty(path, srcValue) {
      if (isKey(path) && isStrictComparable(srcValue)) {
        return matchesStrictComparable(toKey(path), srcValue);
      }
      return function(object) {
        var objValue = get(object, path);
        return (objValue === undefined && objValue === srcValue)
          ? hasIn(object, path)
          : baseIsEqual(srcValue, objValue, COMPARE_PARTIAL_FLAG | COMPARE_UNORDERED_FLAG);
      };
    }

    /**
     * The base implementation of `_.merge` without support for multiple sources.
     *
     * @private
     * @param {Object} object The destination object.
     * @param {Object} source The source object.
     * @param {number} srcIndex The index of `source`.
     * @param {Function} [customizer] The function to customize merged values.
     * @param {Object} [stack] Tracks traversed source values and their merged
     *  counterparts.
     */
    function baseMerge(object, source, srcIndex, customizer, stack) {
      if (object === source) {
        return;
      }
      baseFor(source, function(srcValue, key) {
        stack || (stack = new Stack);
        if (isObject(srcValue)) {
          baseMergeDeep(object, source, key, srcIndex, baseMerge, customizer, stack);
        }
        else {
          var newValue = customizer
            ? customizer(safeGet(object, key), srcValue, (key + ''), object, source, stack)
            : undefined;

          if (newValue === undefined) {
            newValue = srcValue;
          }
          assignMergeValue(object, key, newValue);
        }
      }, keysIn);
    }

    /**
     * A specialized version of `baseMerge` for arrays and objects which performs
     * deep merges and tracks traversed objects enabling objects with circular
     * references to be merged.
     *
     * @private
     * @param {Object} object The destination object.
     * @param {Object} source The source object.
     * @param {string} key The key of the value to merge.
     * @param {number} srcIndex The index of `source`.
     * @param {Function} mergeFunc The function to merge values.
     * @param {Function} [customizer] The function to customize assigned values.
     * @param {Object} [stack] Tracks traversed source values and their merged
     *  counterparts.
     */
    function baseMergeDeep(object, source, key, srcIndex, mergeFunc, customizer, stack) {
      var objValue = safeGet(object, key),
          srcValue = safeGet(source, key),
          stacked = stack.get(srcValue);

      if (stacked) {
        assignMergeValue(object, key, stacked);
        return;
      }
      var newValue = customizer
        ? customizer(objValue, srcValue, (key + ''), object, source, stack)
        : undefined;

      var isCommon = newValue === undefined;

      if (isCommon) {
        var isArr = isArray(srcValue),
            isBuff = !isArr && isBuffer(srcValue),
            isTyped = !isArr && !isBuff && isTypedArray(srcValue);

        newValue = srcValue;
        if (isArr || isBuff || isTyped) {
          if (isArray(objValue)) {
            newValue = objValue;
          }
          else if (isArrayLikeObject(objValue)) {
            newValue = copyArray(objValue);
          }
          else if (isBuff) {
            isCommon = false;
            newValue = cloneBuffer(srcValue, true);
          }
          else if (isTyped) {
            isCommon = false;
            newValue = cloneTypedArray(srcValue, true);
          }
          else {
            newValue = [];
          }
        }
        else if (isPlainObject(srcValue) || isArguments(srcValue)) {
          newValue = objValue;
          if (isArguments(objValue)) {
            newValue = toPlainObject(objValue);
          }
          else if (!isObject(objValue) || isFunction(objValue)) {
            newValue = initCloneObject(srcValue);
          }
        }
        else {
          isCommon = false;
        }
      }
      if (isCommon) {
        // Recursively merge objects and arrays (susceptible to call stack limits).
        stack.set(srcValue, newValue);
        mergeFunc(newValue, srcValue, srcIndex, customizer, stack);
        stack['delete'](srcValue);
      }
      assignMergeValue(object, key, newValue);
    }

    /**
     * The base implementation of `_.nth` which doesn't coerce arguments.
     *
     * @private
     * @param {Array} array The array to query.
     * @param {number} n The index of the element to return.
     * @returns {*} Returns the nth element of `array`.
     */
    function baseNth(array, n) {
      var length = array.length;
      if (!length) {
        return;
      }
      n += n < 0 ? length : 0;
      return isIndex(n, length) ? array[n] : undefined;
    }

    /**
     * The base implementation of `_.orderBy` without param guards.
     *
     * @private
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function[]|Object[]|string[]} iteratees The iteratees to sort by.
     * @param {string[]} orders The sort orders of `iteratees`.
     * @returns {Array} Returns the new sorted array.
     */
    function baseOrderBy(collection, iteratees, orders) {
      if (iteratees.length) {
        iteratees = arrayMap(iteratees, function(iteratee) {
          if (isArray(iteratee)) {
            return function(value) {
              return baseGet(value, iteratee.length === 1 ? iteratee[0] : iteratee);
            }
          }
          return iteratee;
        });
      } else {
        iteratees = [identity];
      }

      var index = -1;
      iteratees = arrayMap(iteratees, baseUnary(getIteratee()));

      var result = baseMap(collection, function(value, key, collection) {
        var criteria = arrayMap(iteratees, function(iteratee) {
          return iteratee(value);
        });
        return { 'criteria': criteria, 'index': ++index, 'value': value };
      });

      return baseSortBy(result, function(object, other) {
        return compareMultiple(object, other, orders);
      });
    }

    /**
     * The base implementation of `_.pick` without support for individual
     * property identifiers.
     *
     * @private
     * @param {Object} object The source object.
     * @param {string[]} paths The property paths to pick.
     * @returns {Object} Returns the new object.
     */
    function basePick(object, paths) {
      return basePickBy(object, paths, function(value, path) {
        return hasIn(object, path);
      });
    }

    /**
     * The base implementation of  `_.pickBy` without support for iteratee shorthands.
     *
     * @private
     * @param {Object} object The source object.
     * @param {string[]} paths The property paths to pick.
     * @param {Function} predicate The function invoked per property.
     * @returns {Object} Returns the new object.
     */
    function basePickBy(object, paths, predicate) {
      var index = -1,
          length = paths.length,
          result = {};

      while (++index < length) {
        var path = paths[index],
            value = baseGet(object, path);

        if (predicate(value, path)) {
          baseSet(result, castPath(path, object), value);
        }
      }
      return result;
    }

    /**
     * A specialized version of `baseProperty` which supports deep paths.
     *
     * @private
     * @param {Array|string} path The path of the property to get.
     * @returns {Function} Returns the new accessor function.
     */
    function basePropertyDeep(path) {
      return function(object) {
        return baseGet(object, path);
      };
    }

    /**
     * The base implementation of `_.pullAllBy` without support for iteratee
     * shorthands.
     *
     * @private
     * @param {Array} array The array to modify.
     * @param {Array} values The values to remove.
     * @param {Function} [iteratee] The iteratee invoked per element.
     * @param {Function} [comparator] The comparator invoked per element.
     * @returns {Array} Returns `array`.
     */
    function basePullAll(array, values, iteratee, comparator) {
      var indexOf = comparator ? baseIndexOfWith : baseIndexOf,
          index = -1,
          length = values.length,
          seen = array;

      if (array === values) {
        values = copyArray(values);
      }
      if (iteratee) {
        seen = arrayMap(array, baseUnary(iteratee));
      }
      while (++index < length) {
        var fromIndex = 0,
            value = values[index],
            computed = iteratee ? iteratee(value) : value;

        while ((fromIndex = indexOf(seen, computed, fromIndex, comparator)) > -1) {
          if (seen !== array) {
            splice.call(seen, fromIndex, 1);
          }
          splice.call(array, fromIndex, 1);
        }
      }
      return array;
    }

    /**
     * The base implementation of `_.pullAt` without support for individual
     * indexes or capturing the removed elements.
     *
     * @private
     * @param {Array} array The array to modify.
     * @param {number[]} indexes The indexes of elements to remove.
     * @returns {Array} Returns `array`.
     */
    function basePullAt(array, indexes) {
      var length = array ? indexes.length : 0,
          lastIndex = length - 1;

      while (length--) {
        var index = indexes[length];
        if (length == lastIndex || index !== previous) {
          var previous = index;
          if (isIndex(index)) {
            splice.call(array, index, 1);
          } else {
            baseUnset(array, index);
          }
        }
      }
      return array;
    }

    /**
     * The base implementation of `_.random` without support for returning
     * floating-point numbers.
     *
     * @private
     * @param {number} lower The lower bound.
     * @param {number} upper The upper bound.
     * @returns {number} Returns the random number.
     */
    function baseRandom(lower, upper) {
      return lower + nativeFloor(nativeRandom() * (upper - lower + 1));
    }

    /**
     * The base implementation of `_.range` and `_.rangeRight` which doesn't
     * coerce arguments.
     *
     * @private
     * @param {number} start The start of the range.
     * @param {number} end The end of the range.
     * @param {number} step The value to increment or decrement by.
     * @param {boolean} [fromRight] Specify iterating from right to left.
     * @returns {Array} Returns the range of numbers.
     */
    function baseRange(start, end, step, fromRight) {
      var index = -1,
          length = nativeMax(nativeCeil((end - start) / (step || 1)), 0),
          result = Array(length);

      while (length--) {
        result[fromRight ? length : ++index] = start;
        start += step;
      }
      return result;
    }

    /**
     * The base implementation of `_.repeat` which doesn't coerce arguments.
     *
     * @private
     * @param {string} string The string to repeat.
     * @param {number} n The number of times to repeat the string.
     * @returns {string} Returns the repeated string.
     */
    function baseRepeat(string, n) {
      var result = '';
      if (!string || n < 1 || n > MAX_SAFE_INTEGER) {
        return result;
      }
      // Leverage the exponentiation by squaring algorithm for a faster repeat.
      // See https://en.wikipedia.org/wiki/Exponentiation_by_squaring for more details.
      do {
        if (n % 2) {
          result += string;
        }
        n = nativeFloor(n / 2);
        if (n) {
          string += string;
        }
      } while (n);

      return result;
    }

    /**
     * The base implementation of `_.rest` which doesn't validate or coerce arguments.
     *
     * @private
     * @param {Function} func The function to apply a rest parameter to.
     * @param {number} [start=func.length-1] The start position of the rest parameter.
     * @returns {Function} Returns the new function.
     */
    function baseRest(func, start) {
      return setToString(overRest(func, start, identity), func + '');
    }

    /**
     * The base implementation of `_.sample`.
     *
     * @private
     * @param {Array|Object} collection The collection to sample.
     * @returns {*} Returns the random element.
     */
    function baseSample(collection) {
      return arraySample(values(collection));
    }

    /**
     * The base implementation of `_.sampleSize` without param guards.
     *
     * @private
     * @param {Array|Object} collection The collection to sample.
     * @param {number} n The number of elements to sample.
     * @returns {Array} Returns the random elements.
     */
    function baseSampleSize(collection, n) {
      var array = values(collection);
      return shuffleSelf(array, baseClamp(n, 0, array.length));
    }

    /**
     * The base implementation of `_.set`.
     *
     * @private
     * @param {Object} object The object to modify.
     * @param {Array|string} path The path of the property to set.
     * @param {*} value The value to set.
     * @param {Function} [customizer] The function to customize path creation.
     * @returns {Object} Returns `object`.
     */
    function baseSet(object, path, value, customizer) {
      if (!isObject(object)) {
        return object;
      }
      path = castPath(path, object);

      var index = -1,
          length = path.length,
          lastIndex = length - 1,
          nested = object;

      while (nested != null && ++index < length) {
        var key = toKey(path[index]),
            newValue = value;

        if (key === '__proto__' || key === 'constructor' || key === 'prototype') {
          return object;
        }

        if (index != lastIndex) {
          var objValue = nested[key];
          newValue = customizer ? customizer(objValue, key, nested) : undefined;
          if (newValue === undefined) {
            newValue = isObject(objValue)
              ? objValue
              : (isIndex(path[index + 1]) ? [] : {});
          }
        }
        assignValue(nested, key, newValue);
        nested = nested[key];
      }
      return object;
    }

    /**
     * The base implementation of `setData` without support for hot loop shorting.
     *
     * @private
     * @param {Function} func The function to associate metadata with.
     * @param {*} data The metadata.
     * @returns {Function} Returns `func`.
     */
    var baseSetData = !metaMap ? identity : function(func, data) {
      metaMap.set(func, data);
      return func;
    };

    /**
     * The base implementation of `setToString` without support for hot loop shorting.
     *
     * @private
     * @param {Function} func The function to modify.
     * @param {Function} string The `toString` result.
     * @returns {Function} Returns `func`.
     */
    var baseSetToString = !defineProperty ? identity : function(func, string) {
      return defineProperty(func, 'toString', {
        'configurable': true,
        'enumerable': false,
        'value': constant(string),
        'writable': true
      });
    };

    /**
     * The base implementation of `_.shuffle`.
     *
     * @private
     * @param {Array|Object} collection The collection to shuffle.
     * @returns {Array} Returns the new shuffled array.
     */
    function baseShuffle(collection) {
      return shuffleSelf(values(collection));
    }

    /**
     * The base implementation of `_.slice` without an iteratee call guard.
     *
     * @private
     * @param {Array} array The array to slice.
     * @param {number} [start=0] The start position.
     * @param {number} [end=array.length] The end position.
     * @returns {Array} Returns the slice of `array`.
     */
    function baseSlice(array, start, end) {
      var index = -1,
          length = array.length;

      if (start < 0) {
        start = -start > length ? 0 : (length + start);
      }
      end = end > length ? length : end;
      if (end < 0) {
        end += length;
      }
      length = start > end ? 0 : ((end - start) >>> 0);
      start >>>= 0;

      var result = Array(length);
      while (++index < length) {
        result[index] = array[index + start];
      }
      return result;
    }

    /**
     * The base implementation of `_.some` without support for iteratee shorthands.
     *
     * @private
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} predicate The function invoked per iteration.
     * @returns {boolean} Returns `true` if any element passes the predicate check,
     *  else `false`.
     */
    function baseSome(collection, predicate) {
      var result;

      baseEach(collection, function(value, index, collection) {
        result = predicate(value, index, collection);
        return !result;
      });
      return !!result;
    }

    /**
     * The base implementation of `_.sortedIndex` and `_.sortedLastIndex` which
     * performs a binary search of `array` to determine the index at which `value`
     * should be inserted into `array` in order to maintain its sort order.
     *
     * @private
     * @param {Array} array The sorted array to inspect.
     * @param {*} value The value to evaluate.
     * @param {boolean} [retHighest] Specify returning the highest qualified index.
     * @returns {number} Returns the index at which `value` should be inserted
     *  into `array`.
     */
    function baseSortedIndex(array, value, retHighest) {
      var low = 0,
          high = array == null ? low : array.length;

      if (typeof value == 'number' && value === value && high <= HALF_MAX_ARRAY_LENGTH) {
        while (low < high) {
          var mid = (low + high) >>> 1,
              computed = array[mid];

          if (computed !== null && !isSymbol(computed) &&
              (retHighest ? (computed <= value) : (computed < value))) {
            low = mid + 1;
          } else {
            high = mid;
          }
        }
        return high;
      }
      return baseSortedIndexBy(array, value, identity, retHighest);
    }

    /**
     * The base implementation of `_.sortedIndexBy` and `_.sortedLastIndexBy`
     * which invokes `iteratee` for `value` and each element of `array` to compute
     * their sort ranking. The iteratee is invoked with one argument; (value).
     *
     * @private
     * @param {Array} array The sorted array to inspect.
     * @param {*} value The value to evaluate.
     * @param {Function} iteratee The iteratee invoked per element.
     * @param {boolean} [retHighest] Specify returning the highest qualified index.
     * @returns {number} Returns the index at which `value` should be inserted
     *  into `array`.
     */
    function baseSortedIndexBy(array, value, iteratee, retHighest) {
      var low = 0,
          high = array == null ? 0 : array.length;
      if (high === 0) {
        return 0;
      }

      value = iteratee(value);
      var valIsNaN = value !== value,
          valIsNull = value === null,
          valIsSymbol = isSymbol(value),
          valIsUndefined = value === undefined;

      while (low < high) {
        var mid = nativeFloor((low + high) / 2),
            computed = iteratee(array[mid]),
            othIsDefined = computed !== undefined,
            othIsNull = computed === null,
            othIsReflexive = computed === computed,
            othIsSymbol = isSymbol(computed);

        if (valIsNaN) {
          var setLow = retHighest || othIsReflexive;
        } else if (valIsUndefined) {
          setLow = othIsReflexive && (retHighest || othIsDefined);
        } else if (valIsNull) {
          setLow = othIsReflexive && othIsDefined && (retHighest || !othIsNull);
        } else if (valIsSymbol) {
          setLow = othIsReflexive && othIsDefined && !othIsNull && (retHighest || !othIsSymbol);
        } else if (othIsNull || othIsSymbol) {
          setLow = false;
        } else {
          setLow = retHighest ? (computed <= value) : (computed < value);
        }
        if (setLow) {
          low = mid + 1;
        } else {
          high = mid;
        }
      }
      return nativeMin(high, MAX_ARRAY_INDEX);
    }

    /**
     * The base implementation of `_.sortedUniq` and `_.sortedUniqBy` without
     * support for iteratee shorthands.
     *
     * @private
     * @param {Array} array The array to inspect.
     * @param {Function} [iteratee] The iteratee invoked per element.
     * @returns {Array} Returns the new duplicate free array.
     */
    function baseSortedUniq(array, iteratee) {
      var index = -1,
          length = array.length,
          resIndex = 0,
          result = [];

      while (++index < length) {
        var value = array[index],
            computed = iteratee ? iteratee(value) : value;

        if (!index || !eq(computed, seen)) {
          var seen = computed;
          result[resIndex++] = value === 0 ? 0 : value;
        }
      }
      return result;
    }

    /**
     * The base implementation of `_.toNumber` which doesn't ensure correct
     * conversions of binary, hexadecimal, or octal string values.
     *
     * @private
     * @param {*} value The value to process.
     * @returns {number} Returns the number.
     */
    function baseToNumber(value) {
      if (typeof value == 'number') {
        return value;
      }
      if (isSymbol(value)) {
        return NAN;
      }
      return +value;
    }

    /**
     * The base implementation of `_.toString` which doesn't convert nullish
     * values to empty strings.
     *
     * @private
     * @param {*} value The value to process.
     * @returns {string} Returns the string.
     */
    function baseToString(value) {
      // Exit early for strings to avoid a performance hit in some environments.
      if (typeof value == 'string') {
        return value;
      }
      if (isArray(value)) {
        // Recursively convert values (susceptible to call stack limits).
        return arrayMap(value, baseToString) + '';
      }
      if (isSymbol(value)) {
        return symbolToString ? symbolToString.call(value) : '';
      }
      var result = (value + '');
      return (result == '0' && (1 / value) == -INFINITY) ? '-0' : result;
    }

    /**
     * The base implementation of `_.uniqBy` without support for iteratee shorthands.
     *
     * @private
     * @param {Array} array The array to inspect.
     * @param {Function} [iteratee] The iteratee invoked per element.
     * @param {Function} [comparator] The comparator invoked per element.
     * @returns {Array} Returns the new duplicate free array.
     */
    function baseUniq(array, iteratee, comparator) {
      var index = -1,
          includes = arrayIncludes,
          length = array.length,
          isCommon = true,
          result = [],
          seen = result;

      if (comparator) {
        isCommon = false;
        includes = arrayIncludesWith;
      }
      else if (length >= LARGE_ARRAY_SIZE) {
        var set = iteratee ? null : createSet(array);
        if (set) {
          return setToArray(set);
        }
        isCommon = false;
        includes = cacheHas;
        seen = new SetCache;
      }
      else {
        seen = iteratee ? [] : result;
      }
      outer:
      while (++index < length) {
        var value = array[index],
            computed = iteratee ? iteratee(value) : value;

        value = (comparator || value !== 0) ? value : 0;
        if (isCommon && computed === computed) {
          var seenIndex = seen.length;
          while (seenIndex--) {
            if (seen[seenIndex] === computed) {
              continue outer;
            }
          }
          if (iteratee) {
            seen.push(computed);
          }
          result.push(value);
        }
        else if (!includes(seen, computed, comparator)) {
          if (seen !== result) {
            seen.push(computed);
          }
          result.push(value);
        }
      }
      return result;
    }

    /**
     * The base implementation of `_.unset`.
     *
     * @private
     * @param {Object} object The object to modify.
     * @param {Array|string} path The property path to unset.
     * @returns {boolean} Returns `true` if the property is deleted, else `false`.
     */
    function baseUnset(object, path) {
      path = castPath(path, object);
      object = parent(object, path);
      return object == null || delete object[toKey(last(path))];
    }

    /**
     * The base implementation of `_.update`.
     *
     * @private
     * @param {Object} object The object to modify.
     * @param {Array|string} path The path of the property to update.
     * @param {Function} updater The function to produce the updated value.
     * @param {Function} [customizer] The function to customize path creation.
     * @returns {Object} Returns `object`.
     */
    function baseUpdate(object, path, updater, customizer) {
      return baseSet(object, path, updater(baseGet(object, path)), customizer);
    }

    /**
     * The base implementation of methods like `_.dropWhile` and `_.takeWhile`
     * without support for iteratee shorthands.
     *
     * @private
     * @param {Array} array The array to query.
     * @param {Function} predicate The function invoked per iteration.
     * @param {boolean} [isDrop] Specify dropping elements instead of taking them.
     * @param {boolean} [fromRight] Specify iterating from right to left.
     * @returns {Array} Returns the slice of `array`.
     */
    function baseWhile(array, predicate, isDrop, fromRight) {
      var length = array.length,
          index = fromRight ? length : -1;

      while ((fromRight ? index-- : ++index < length) &&
        predicate(array[index], index, array)) {}

      return isDrop
        ? baseSlice(array, (fromRight ? 0 : index), (fromRight ? index + 1 : length))
        : baseSlice(array, (fromRight ? index + 1 : 0), (fromRight ? length : index));
    }

    /**
     * The base implementation of `wrapperValue` which returns the result of
     * performing a sequence of actions on the unwrapped `value`, where each
     * successive action is supplied the return value of the previous.
     *
     * @private
     * @param {*} value The unwrapped value.
     * @param {Array} actions Actions to perform to resolve the unwrapped value.
     * @returns {*} Returns the resolved value.
     */
    function baseWrapperValue(value, actions) {
      var result = value;
      if (result instanceof LazyWrapper) {
        result = result.value();
      }
      return arrayReduce(actions, function(result, action) {
        return action.func.apply(action.thisArg, arrayPush([result], action.args));
      }, result);
    }

    /**
     * The base implementation of methods like `_.xor`, without support for
     * iteratee shorthands, that accepts an array of arrays to inspect.
     *
     * @private
     * @param {Array} arrays The arrays to inspect.
     * @param {Function} [iteratee] The iteratee invoked per element.
     * @param {Function} [comparator] The comparator invoked per element.
     * @returns {Array} Returns the new array of values.
     */
    function baseXor(arrays, iteratee, comparator) {
      var length = arrays.length;
      if (length < 2) {
        return length ? baseUniq(arrays[0]) : [];
      }
      var index = -1,
          result = Array(length);

      while (++index < length) {
        var array = arrays[index],
            othIndex = -1;

        while (++othIndex < length) {
          if (othIndex != index) {
            result[index] = baseDifference(result[index] || array, arrays[othIndex], iteratee, comparator);
          }
        }
      }
      return baseUniq(baseFlatten(result, 1), iteratee, comparator);
    }

    /**
     * This base implementation of `_.zipObject` which assigns values using `assignFunc`.
     *
     * @private
     * @param {Array} props The property identifiers.
     * @param {Array} values The property values.
     * @param {Function} assignFunc The function to assign values.
     * @returns {Object} Returns the new object.
     */
    function baseZipObject(props, values, assignFunc) {
      var index = -1,
          length = props.length,
          valsLength = values.length,
          result = {};

      while (++index < length) {
        var value = index < valsLength ? values[index] : undefined;
        assignFunc(result, props[index], value);
      }
      return result;
    }

    /**
     * Casts `value` to an empty array if it's not an array like object.
     *
     * @private
     * @param {*} value The value to inspect.
     * @returns {Array|Object} Returns the cast array-like object.
     */
    function castArrayLikeObject(value) {
      return isArrayLikeObject(value) ? value : [];
    }

    /**
     * Casts `value` to `identity` if it's not a function.
     *
     * @private
     * @param {*} value The value to inspect.
     * @returns {Function} Returns cast function.
     */
    function castFunction(value) {
      return typeof value == 'function' ? value : identity;
    }

    /**
     * Casts `value` to a path array if it's not one.
     *
     * @private
     * @param {*} value The value to inspect.
     * @param {Object} [object] The object to query keys on.
     * @returns {Array} Returns the cast property path array.
     */
    function castPath(value, object) {
      if (isArray(value)) {
        return value;
      }
      return isKey(value, object) ? [value] : stringToPath(toString(value));
    }

    /**
     * A `baseRest` alias which can be replaced with `identity` by module
     * replacement plugins.
     *
     * @private
     * @type {Function}
     * @param {Function} func The function to apply a rest parameter to.
     * @returns {Function} Returns the new function.
     */
    var castRest = baseRest;

    /**
     * Casts `array` to a slice if it's needed.
     *
     * @private
     * @param {Array} array The array to inspect.
     * @param {number} start The start position.
     * @param {number} [end=array.length] The end position.
     * @returns {Array} Returns the cast slice.
     */
    function castSlice(array, start, end) {
      var length = array.length;
      end = end === undefined ? length : end;
      return (!start && end >= length) ? array : baseSlice(array, start, end);
    }

    /**
     * A simple wrapper around the global [`clearTimeout`](https://mdn.io/clearTimeout).
     *
     * @private
     * @param {number|Object} id The timer id or timeout object of the timer to clear.
     */
    var clearTimeout = ctxClearTimeout || function(id) {
      return root.clearTimeout(id);
    };

    /**
     * Creates a clone of  `buffer`.
     *
     * @private
     * @param {Buffer} buffer The buffer to clone.
     * @param {boolean} [isDeep] Specify a deep clone.
     * @returns {Buffer} Returns the cloned buffer.
     */
    function cloneBuffer(buffer, isDeep) {
      if (isDeep) {
        return buffer.slice();
      }
      var length = buffer.length,
          result = allocUnsafe ? allocUnsafe(length) : new buffer.constructor(length);

      buffer.copy(result);
      return result;
    }

    /**
     * Creates a clone of `arrayBuffer`.
     *
     * @private
     * @param {ArrayBuffer} arrayBuffer The array buffer to clone.
     * @returns {ArrayBuffer} Returns the cloned array buffer.
     */
    function cloneArrayBuffer(arrayBuffer) {
      var result = new arrayBuffer.constructor(arrayBuffer.byteLength);
      new Uint8Array(result).set(new Uint8Array(arrayBuffer));
      return result;
    }

    /**
     * Creates a clone of `dataView`.
     *
     * @private
     * @param {Object} dataView The data view to clone.
     * @param {boolean} [isDeep] Specify a deep clone.
     * @returns {Object} Returns the cloned data view.
     */
    function cloneDataView(dataView, isDeep) {
      var buffer = isDeep ? cloneArrayBuffer(dataView.buffer) : dataView.buffer;
      return new dataView.constructor(buffer, dataView.byteOffset, dataView.byteLength);
    }

    /**
     * Creates a clone of `regexp`.
     *
     * @private
     * @param {Object} regexp The regexp to clone.
     * @returns {Object} Returns the cloned regexp.
     */
    function cloneRegExp(regexp) {
      var result = new regexp.constructor(regexp.source, reFlags.exec(regexp));
      result.lastIndex = regexp.lastIndex;
      return result;
    }

    /**
     * Creates a clone of the `symbol` object.
     *
     * @private
     * @param {Object} symbol The symbol object to clone.
     * @returns {Object} Returns the cloned symbol object.
     */
    function cloneSymbol(symbol) {
      return symbolValueOf ? Object(symbolValueOf.call(symbol)) : {};
    }

    /**
     * Creates a clone of `typedArray`.
     *
     * @private
     * @param {Object} typedArray The typed array to clone.
     * @param {boolean} [isDeep] Specify a deep clone.
     * @returns {Object} Returns the cloned typed array.
     */
    function cloneTypedArray(typedArray, isDeep) {
      var buffer = isDeep ? cloneArrayBuffer(typedArray.buffer) : typedArray.buffer;
      return new typedArray.constructor(buffer, typedArray.byteOffset, typedArray.length);
    }

    /**
     * Compares values to sort them in ascending order.
     *
     * @private
     * @param {*} value The value to compare.
     * @param {*} other The other value to compare.
     * @returns {number} Returns the sort order indicator for `value`.
     */
    function compareAscending(value, other) {
      if (value !== other) {
        var valIsDefined = value !== undefined,
            valIsNull = value === null,
            valIsReflexive = value === value,
            valIsSymbol = isSymbol(value);

        var othIsDefined = other !== undefined,
            othIsNull = other === null,
            othIsReflexive = other === other,
            othIsSymbol = isSymbol(other);

        if ((!othIsNull && !othIsSymbol && !valIsSymbol && value > other) ||
            (valIsSymbol && othIsDefined && othIsReflexive && !othIsNull && !othIsSymbol) ||
            (valIsNull && othIsDefined && othIsReflexive) ||
            (!valIsDefined && othIsReflexive) ||
            !valIsReflexive) {
          return 1;
        }
        if ((!valIsNull && !valIsSymbol && !othIsSymbol && value < other) ||
            (othIsSymbol && valIsDefined && valIsReflexive && !valIsNull && !valIsSymbol) ||
            (othIsNull && valIsDefined && valIsReflexive) ||
            (!othIsDefined && valIsReflexive) ||
            !othIsReflexive) {
          return -1;
        }
      }
      return 0;
    }

    /**
     * Used by `_.orderBy` to compare multiple properties of a value to another
     * and stable sort them.
     *
     * If `orders` is unspecified, all values are sorted in ascending order. Otherwise,
     * specify an order of "desc" for descending or "asc" for ascending sort order
     * of corresponding values.
     *
     * @private
     * @param {Object} object The object to compare.
     * @param {Object} other The other object to compare.
     * @param {boolean[]|string[]} orders The order to sort by for each property.
     * @returns {number} Returns the sort order indicator for `object`.
     */
    function compareMultiple(object, other, orders) {
      var index = -1,
          objCriteria = object.criteria,
          othCriteria = other.criteria,
          length = objCriteria.length,
          ordersLength = orders.length;

      while (++index < length) {
        var result = compareAscending(objCriteria[index], othCriteria[index]);
        if (result) {
          if (index >= ordersLength) {
            return result;
          }
          var order = orders[index];
          return result * (order == 'desc' ? -1 : 1);
        }
      }
      // Fixes an `Array#sort` bug in the JS engine embedded in Adobe applications
      // that causes it, under certain circumstances, to provide the same value for
      // `object` and `other`. See https://github.com/jashkenas/underscore/pull/1247
      // for more details.
      //
      // This also ensures a stable sort in V8 and other engines.
      // See https://bugs.chromium.org/p/v8/issues/detail?id=90 for more details.
      return object.index - other.index;
    }

    /**
     * Creates an array that is the composition of partially applied arguments,
     * placeholders, and provided arguments into a single array of arguments.
     *
     * @private
     * @param {Array} args The provided arguments.
     * @param {Array} partials The arguments to prepend to those provided.
     * @param {Array} holders The `partials` placeholder indexes.
     * @params {boolean} [isCurried] Specify composing for a curried function.
     * @returns {Array} Returns the new array of composed arguments.
     */
    function composeArgs(args, partials, holders, isCurried) {
      var argsIndex = -1,
          argsLength = args.length,
          holdersLength = holders.length,
          leftIndex = -1,
          leftLength = partials.length,
          rangeLength = nativeMax(argsLength - holdersLength, 0),
          result = Array(leftLength + rangeLength),
          isUncurried = !isCurried;

      while (++leftIndex < leftLength) {
        result[leftIndex] = partials[leftIndex];
      }
      while (++argsIndex < holdersLength) {
        if (isUncurried || argsIndex < argsLength) {
          result[holders[argsIndex]] = args[argsIndex];
        }
      }
      while (rangeLength--) {
        result[leftIndex++] = args[argsIndex++];
      }
      return result;
    }

    /**
     * This function is like `composeArgs` except that the arguments composition
     * is tailored for `_.partialRight`.
     *
     * @private
     * @param {Array} args The provided arguments.
     * @param {Array} partials The arguments to append to those provided.
     * @param {Array} holders The `partials` placeholder indexes.
     * @params {boolean} [isCurried] Specify composing for a curried function.
     * @returns {Array} Returns the new array of composed arguments.
     */
    function composeArgsRight(args, partials, holders, isCurried) {
      var argsIndex = -1,
          argsLength = args.length,
          holdersIndex = -1,
          holdersLength = holders.length,
          rightIndex = -1,
          rightLength = partials.length,
          rangeLength = nativeMax(argsLength - holdersLength, 0),
          result = Array(rangeLength + rightLength),
          isUncurried = !isCurried;

      while (++argsIndex < rangeLength) {
        result[argsIndex] = args[argsIndex];
      }
      var offset = argsIndex;
      while (++rightIndex < rightLength) {
        result[offset + rightIndex] = partials[rightIndex];
      }
      while (++holdersIndex < holdersLength) {
        if (isUncurried || argsIndex < argsLength) {
          result[offset + holders[holdersIndex]] = args[argsIndex++];
        }
      }
      return result;
    }

    /**
     * Copies the values of `source` to `array`.
     *
     * @private
     * @param {Array} source The array to copy values from.
     * @param {Array} [array=[]] The array to copy values to.
     * @returns {Array} Returns `array`.
     */
    function copyArray(source, array) {
      var index = -1,
          length = source.length;

      array || (array = Array(length));
      while (++index < length) {
        array[index] = source[index];
      }
      return array;
    }

    /**
     * Copies properties of `source` to `object`.
     *
     * @private
     * @param {Object} source The object to copy properties from.
     * @param {Array} props The property identifiers to copy.
     * @param {Object} [object={}] The object to copy properties to.
     * @param {Function} [customizer] The function to customize copied values.
     * @returns {Object} Returns `object`.
     */
    function copyObject(source, props, object, customizer) {
      var isNew = !object;
      object || (object = {});

      var index = -1,
          length = props.length;

      while (++index < length) {
        var key = props[index];

        var newValue = customizer
          ? customizer(object[key], source[key], key, object, source)
          : undefined;

        if (newValue === undefined) {
          newValue = source[key];
        }
        if (isNew) {
          baseAssignValue(object, key, newValue);
        } else {
          assignValue(object, key, newValue);
        }
      }
      return object;
    }

    /**
     * Copies own symbols of `source` to `object`.
     *
     * @private
     * @param {Object} source The object to copy symbols from.
     * @param {Object} [object={}] The object to copy symbols to.
     * @returns {Object} Returns `object`.
     */
    function copySymbols(source, object) {
      return copyObject(source, getSymbols(source), object);
    }

    /**
     * Copies own and inherited symbols of `source` to `object`.
     *
     * @private
     * @param {Object} source The object to copy symbols from.
     * @param {Object} [object={}] The object to copy symbols to.
     * @returns {Object} Returns `object`.
     */
    function copySymbolsIn(source, object) {
      return copyObject(source, getSymbolsIn(source), object);
    }

    /**
     * Creates a function like `_.groupBy`.
     *
     * @private
     * @param {Function} setter The function to set accumulator values.
     * @param {Function} [initializer] The accumulator object initializer.
     * @returns {Function} Returns the new aggregator function.
     */
    function createAggregator(setter, initializer) {
      return function(collection, iteratee) {
        var func = isArray(collection) ? arrayAggregator : baseAggregator,
            accumulator = initializer ? initializer() : {};

        return func(collection, setter, getIteratee(iteratee, 2), accumulator);
      };
    }

    /**
     * Creates a function like `_.assign`.
     *
     * @private
     * @param {Function} assigner The function to assign values.
     * @returns {Function} Returns the new assigner function.
     */
    function createAssigner(assigner) {
      return baseRest(function(object, sources) {
        var index = -1,
            length = sources.length,
            customizer = length > 1 ? sources[length - 1] : undefined,
            guard = length > 2 ? sources[2] : undefined;

        customizer = (assigner.length > 3 && typeof customizer == 'function')
          ? (length--, customizer)
          : undefined;

        if (guard && isIterateeCall(sources[0], sources[1], guard)) {
          customizer = length < 3 ? undefined : customizer;
          length = 1;
        }
        object = Object(object);
        while (++index < length) {
          var source = sources[index];
          if (source) {
            assigner(object, source, index, customizer);
          }
        }
        return object;
      });
    }

    /**
     * Creates a `baseEach` or `baseEachRight` function.
     *
     * @private
     * @param {Function} eachFunc The function to iterate over a collection.
     * @param {boolean} [fromRight] Specify iterating from right to left.
     * @returns {Function} Returns the new base function.
     */
    function createBaseEach(eachFunc, fromRight) {
      return function(collection, iteratee) {
        if (collection == null) {
          return collection;
        }
        if (!isArrayLike(collection)) {
          return eachFunc(collection, iteratee);
        }
        var length = collection.length,
            index = fromRight ? length : -1,
            iterable = Object(collection);

        while ((fromRight ? index-- : ++index < length)) {
          if (iteratee(iterable[index], index, iterable) === false) {
            break;
          }
        }
        return collection;
      };
    }

    /**
     * Creates a base function for methods like `_.forIn` and `_.forOwn`.
     *
     * @private
     * @param {boolean} [fromRight] Specify iterating from right to left.
     * @returns {Function} Returns the new base function.
     */
    function createBaseFor(fromRight) {
      return function(object, iteratee, keysFunc) {
        var index = -1,
            iterable = Object(object),
            props = keysFunc(object),
            length = props.length;

        while (length--) {
          var key = props[fromRight ? length : ++index];
          if (iteratee(iterable[key], key, iterable) === false) {
            break;
          }
        }
        return object;
      };
    }

    /**
     * Creates a function that wraps `func` to invoke it with the optional `this`
     * binding of `thisArg`.
     *
     * @private
     * @param {Function} func The function to wrap.
     * @param {number} bitmask The bitmask flags. See `createWrap` for more details.
     * @param {*} [thisArg] The `this` binding of `func`.
     * @returns {Function} Returns the new wrapped function.
     */
    function createBind(func, bitmask, thisArg) {
      var isBind = bitmask & WRAP_BIND_FLAG,
          Ctor = createCtor(func);

      function wrapper() {
        var fn = (this && this !== root && this instanceof wrapper) ? Ctor : func;
        return fn.apply(isBind ? thisArg : this, arguments);
      }
      return wrapper;
    }

    /**
     * Creates a function like `_.lowerFirst`.
     *
     * @private
     * @param {string} methodName The name of the `String` case method to use.
     * @returns {Function} Returns the new case function.
     */
    function createCaseFirst(methodName) {
      return function(string) {
        string = toString(string);

        var strSymbols = hasUnicode(string)
          ? stringToArray(string)
          : undefined;

        var chr = strSymbols
          ? strSymbols[0]
          : string.charAt(0);

        var trailing = strSymbols
          ? castSlice(strSymbols, 1).join('')
          : string.slice(1);

        return chr[methodName]() + trailing;
      };
    }

    /**
     * Creates a function like `_.camelCase`.
     *
     * @private
     * @param {Function} callback The function to combine each word.
     * @returns {Function} Returns the new compounder function.
     */
    function createCompounder(callback) {
      return function(string) {
        return arrayReduce(words(deburr(string).replace(reApos, '')), callback, '');
      };
    }

    /**
     * Creates a function that produces an instance of `Ctor` regardless of
     * whether it was invoked as part of a `new` expression or by `call` or `apply`.
     *
     * @private
     * @param {Function} Ctor The constructor to wrap.
     * @returns {Function} Returns the new wrapped function.
     */
    function createCtor(Ctor) {
      return function() {
        // Use a `switch` statement to work with class constructors. See
        // http://ecma-international.org/ecma-262/7.0/#sec-ecmascript-function-objects-call-thisargument-argumentslist
        // for more details.
        var args = arguments;
        switch (args.length) {
          case 0: return new Ctor;
          case 1: return new Ctor(args[0]);
          case 2: return new Ctor(args[0], args[1]);
          case 3: return new Ctor(args[0], args[1], args[2]);
          case 4: return new Ctor(args[0], args[1], args[2], args[3]);
          case 5: return new Ctor(args[0], args[1], args[2], args[3], args[4]);
          case 6: return new Ctor(args[0], args[1], args[2], args[3], args[4], args[5]);
          case 7: return new Ctor(args[0], args[1], args[2], args[3], args[4], args[5], args[6]);
        }
        var thisBinding = baseCreate(Ctor.prototype),
            result = Ctor.apply(thisBinding, args);

        // Mimic the constructor's `return` behavior.
        // See https://es5.github.io/#x13.2.2 for more details.
        return isObject(result) ? result : thisBinding;
      };
    }

    /**
     * Creates a function that wraps `func` to enable currying.
     *
     * @private
     * @param {Function} func The function to wrap.
     * @param {number} bitmask The bitmask flags. See `createWrap` for more details.
     * @param {number} arity The arity of `func`.
     * @returns {Function} Returns the new wrapped function.
     */
    function createCurry(func, bitmask, arity) {
      var Ctor = createCtor(func);

      function wrapper() {
        var length = arguments.length,
            args = Array(length),
            index = length,
            placeholder = getHolder(wrapper);

        while (index--) {
          args[index] = arguments[index];
        }
        var holders = (length < 3 && args[0] !== placeholder && args[length - 1] !== placeholder)
          ? []
          : replaceHolders(args, placeholder);

        length -= holders.length;
        if (length < arity) {
          return createRecurry(
            func, bitmask, createHybrid, wrapper.placeholder, undefined,
            args, holders, undefined, undefined, arity - length);
        }
        var fn = (this && this !== root && this instanceof wrapper) ? Ctor : func;
        return apply(fn, this, args);
      }
      return wrapper;
    }

    /**
     * Creates a `_.find` or `_.findLast` function.
     *
     * @private
     * @param {Function} findIndexFunc The function to find the collection index.
     * @returns {Function} Returns the new find function.
     */
    function createFind(findIndexFunc) {
      return function(collection, predicate, fromIndex) {
        var iterable = Object(collection);
        if (!isArrayLike(collection)) {
          var iteratee = getIteratee(predicate, 3);
          collection = keys(collection);
          predicate = function(key) { return iteratee(iterable[key], key, iterable); };
        }
        var index = findIndexFunc(collection, predicate, fromIndex);
        return index > -1 ? iterable[iteratee ? collection[index] : index] : undefined;
      };
    }

    /**
     * Creates a `_.flow` or `_.flowRight` function.
     *
     * @private
     * @param {boolean} [fromRight] Specify iterating from right to left.
     * @returns {Function} Returns the new flow function.
     */
    function createFlow(fromRight) {
      return flatRest(function(funcs) {
        var length = funcs.length,
            index = length,
            prereq = LodashWrapper.prototype.thru;

        if (fromRight) {
          funcs.reverse();
        }
        while (index--) {
          var func = funcs[index];
          if (typeof func != 'function') {
            throw new TypeError(FUNC_ERROR_TEXT);
          }
          if (prereq && !wrapper && getFuncName(func) == 'wrapper') {
            var wrapper = new LodashWrapper([], true);
          }
        }
        index = wrapper ? index : length;
        while (++index < length) {
          func = funcs[index];

          var funcName = getFuncName(func),
              data = funcName == 'wrapper' ? getData(func) : undefined;

          if (data && isLaziable(data[0]) &&
                data[1] == (WRAP_ARY_FLAG | WRAP_CURRY_FLAG | WRAP_PARTIAL_FLAG | WRAP_REARG_FLAG) &&
                !data[4].length && data[9] == 1
              ) {
            wrapper = wrapper[getFuncName(data[0])].apply(wrapper, data[3]);
          } else {
            wrapper = (func.length == 1 && isLaziable(func))
              ? wrapper[funcName]()
              : wrapper.thru(func);
          }
        }
        return function() {
          var args = arguments,
              value = args[0];

          if (wrapper && args.length == 1 && isArray(value)) {
            return wrapper.plant(value).value();
          }
          var index = 0,
              result = length ? funcs[index].apply(this, args) : value;

          while (++index < length) {
            result = funcs[index].call(this, result);
          }
          return result;
        };
      });
    }

    /**
     * Creates a function that wraps `func` to invoke it with optional `this`
     * binding of `thisArg`, partial application, and currying.
     *
     * @private
     * @param {Function|string} func The function or method name to wrap.
     * @param {number} bitmask The bitmask flags. See `createWrap` for more details.
     * @param {*} [thisArg] The `this` binding of `func`.
     * @param {Array} [partials] The arguments to prepend to those provided to
     *  the new function.
     * @param {Array} [holders] The `partials` placeholder indexes.
     * @param {Array} [partialsRight] The arguments to append to those provided
     *  to the new function.
     * @param {Array} [holdersRight] The `partialsRight` placeholder indexes.
     * @param {Array} [argPos] The argument positions of the new function.
     * @param {number} [ary] The arity cap of `func`.
     * @param {number} [arity] The arity of `func`.
     * @returns {Function} Returns the new wrapped function.
     */
    function createHybrid(func, bitmask, thisArg, partials, holders, partialsRight, holdersRight, argPos, ary, arity) {
      var isAry = bitmask & WRAP_ARY_FLAG,
          isBind = bitmask & WRAP_BIND_FLAG,
          isBindKey = bitmask & WRAP_BIND_KEY_FLAG,
          isCurried = bitmask & (WRAP_CURRY_FLAG | WRAP_CURRY_RIGHT_FLAG),
          isFlip = bitmask & WRAP_FLIP_FLAG,
          Ctor = isBindKey ? undefined : createCtor(func);

      function wrapper() {
        var length = arguments.length,
            args = Array(length),
            index = length;

        while (index--) {
          args[index] = arguments[index];
        }
        if (isCurried) {
          var placeholder = getHolder(wrapper),
              holdersCount = countHolders(args, placeholder);
        }
        if (partials) {
          args = composeArgs(args, partials, holders, isCurried);
        }
        if (partialsRight) {
          args = composeArgsRight(args, partialsRight, holdersRight, isCurried);
        }
        length -= holdersCount;
        if (isCurried && length < arity) {
          var newHolders = replaceHolders(args, placeholder);
          return createRecurry(
            func, bitmask, createHybrid, wrapper.placeholder, thisArg,
            args, newHolders, argPos, ary, arity - length
          );
        }
        var thisBinding = isBind ? thisArg : this,
            fn = isBindKey ? thisBinding[func] : func;

        length = args.length;
        if (argPos) {
          args = reorder(args, argPos);
        } else if (isFlip && length > 1) {
          args.reverse();
        }
        if (isAry && ary < length) {
          args.length = ary;
        }
        if (this && this !== root && this instanceof wrapper) {
          fn = Ctor || createCtor(fn);
        }
        return fn.apply(thisBinding, args);
      }
      return wrapper;
    }

    /**
     * Creates a function like `_.invertBy`.
     *
     * @private
     * @param {Function} setter The function to set accumulator values.
     * @param {Function} toIteratee The function to resolve iteratees.
     * @returns {Function} Returns the new inverter function.
     */
    function createInverter(setter, toIteratee) {
      return function(object, iteratee) {
        return baseInverter(object, setter, toIteratee(iteratee), {});
      };
    }

    /**
     * Creates a function that performs a mathematical operation on two values.
     *
     * @private
     * @param {Function} operator The function to perform the operation.
     * @param {number} [defaultValue] The value used for `undefined` arguments.
     * @returns {Function} Returns the new mathematical operation function.
     */
    function createMathOperation(operator, defaultValue) {
      return function(value, other) {
        var result;
        if (value === undefined && other === undefined) {
          return defaultValue;
        }
        if (value !== undefined) {
          result = value;
        }
        if (other !== undefined) {
          if (result === undefined) {
            return other;
          }
          if (typeof value == 'string' || typeof other == 'string') {
            value = baseToString(value);
            other = baseToString(other);
          } else {
            value = baseToNumber(value);
            other = baseToNumber(other);
          }
          result = operator(value, other);
        }
        return result;
      };
    }

    /**
     * Creates a function like `_.over`.
     *
     * @private
     * @param {Function} arrayFunc The function to iterate over iteratees.
     * @returns {Function} Returns the new over function.
     */
    function createOver(arrayFunc) {
      return flatRest(function(iteratees) {
        iteratees = arrayMap(iteratees, baseUnary(getIteratee()));
        return baseRest(function(args) {
          var thisArg = this;
          return arrayFunc(iteratees, function(iteratee) {
            return apply(iteratee, thisArg, args);
          });
        });
      });
    }

    /**
     * Creates the padding for `string` based on `length`. The `chars` string
     * is truncated if the number of characters exceeds `length`.
     *
     * @private
     * @param {number} length The padding length.
     * @param {string} [chars=' '] The string used as padding.
     * @returns {string} Returns the padding for `string`.
     */
    function createPadding(length, chars) {
      chars = chars === undefined ? ' ' : baseToString(chars);

      var charsLength = chars.length;
      if (charsLength < 2) {
        return charsLength ? baseRepeat(chars, length) : chars;
      }
      var result = baseRepeat(chars, nativeCeil(length / stringSize(chars)));
      return hasUnicode(chars)
        ? castSlice(stringToArray(result), 0, length).join('')
        : result.slice(0, length);
    }

    /**
     * Creates a function that wraps `func` to invoke it with the `this` binding
     * of `thisArg` and `partials` prepended to the arguments it receives.
     *
     * @private
     * @param {Function} func The function to wrap.
     * @param {number} bitmask The bitmask flags. See `createWrap` for more details.
     * @param {*} thisArg The `this` binding of `func`.
     * @param {Array} partials The arguments to prepend to those provided to
     *  the new function.
     * @returns {Function} Returns the new wrapped function.
     */
    function createPartial(func, bitmask, thisArg, partials) {
      var isBind = bitmask & WRAP_BIND_FLAG,
          Ctor = createCtor(func);

      function wrapper() {
        var argsIndex = -1,
            argsLength = arguments.length,
            leftIndex = -1,
            leftLength = partials.length,
            args = Array(leftLength + argsLength),
            fn = (this && this !== root && this instanceof wrapper) ? Ctor : func;

        while (++leftIndex < leftLength) {
          args[leftIndex] = partials[leftIndex];
        }
        while (argsLength--) {
          args[leftIndex++] = arguments[++argsIndex];
        }
        return apply(fn, isBind ? thisArg : this, args);
      }
      return wrapper;
    }

    /**
     * Creates a `_.range` or `_.rangeRight` function.
     *
     * @private
     * @param {boolean} [fromRight] Specify iterating from right to left.
     * @returns {Function} Returns the new range function.
     */
    function createRange(fromRight) {
      return function(start, end, step) {
        if (step && typeof step != 'number' && isIterateeCall(start, end, step)) {
          end = step = undefined;
        }
        // Ensure the sign of `-0` is preserved.
        start = toFinite(start);
        if (end === undefined) {
          end = start;
          start = 0;
        } else {
          end = toFinite(end);
        }
        step = step === undefined ? (start < end ? 1 : -1) : toFinite(step);
        return baseRange(start, end, step, fromRight);
      };
    }

    /**
     * Creates a function that performs a relational operation on two values.
     *
     * @private
     * @param {Function} operator The function to perform the operation.
     * @returns {Function} Returns the new relational operation function.
     */
    function createRelationalOperation(operator) {
      return function(value, other) {
        if (!(typeof value == 'string' && typeof other == 'string')) {
          value = toNumber(value);
          other = toNumber(other);
        }
        return operator(value, other);
      };
    }

    /**
     * Creates a function that wraps `func` to continue currying.
     *
     * @private
     * @param {Function} func The function to wrap.
     * @param {number} bitmask The bitmask flags. See `createWrap` for more details.
     * @param {Function} wrapFunc The function to create the `func` wrapper.
     * @param {*} placeholder The placeholder value.
     * @param {*} [thisArg] The `this` binding of `func`.
     * @param {Array} [partials] The arguments to prepend to those provided to
     *  the new function.
     * @param {Array} [holders] The `partials` placeholder indexes.
     * @param {Array} [argPos] The argument positions of the new function.
     * @param {number} [ary] The arity cap of `func`.
     * @param {number} [arity] The arity of `func`.
     * @returns {Function} Returns the new wrapped function.
     */
    function createRecurry(func, bitmask, wrapFunc, placeholder, thisArg, partials, holders, argPos, ary, arity) {
      var isCurry = bitmask & WRAP_CURRY_FLAG,
          newHolders = isCurry ? holders : undefined,
          newHoldersRight = isCurry ? undefined : holders,
          newPartials = isCurry ? partials : undefined,
          newPartialsRight = isCurry ? undefined : partials;

      bitmask |= (isCurry ? WRAP_PARTIAL_FLAG : WRAP_PARTIAL_RIGHT_FLAG);
      bitmask &= ~(isCurry ? WRAP_PARTIAL_RIGHT_FLAG : WRAP_PARTIAL_FLAG);

      if (!(bitmask & WRAP_CURRY_BOUND_FLAG)) {
        bitmask &= ~(WRAP_BIND_FLAG | WRAP_BIND_KEY_FLAG);
      }
      var newData = [
        func, bitmask, thisArg, newPartials, newHolders, newPartialsRight,
        newHoldersRight, argPos, ary, arity
      ];

      var result = wrapFunc.apply(undefined, newData);
      if (isLaziable(func)) {
        setData(result, newData);
      }
      result.placeholder = placeholder;
      return setWrapToString(result, func, bitmask);
    }

    /**
     * Creates a function like `_.round`.
     *
     * @private
     * @param {string} methodName The name of the `Math` method to use when rounding.
     * @returns {Function} Returns the new round function.
     */
    function createRound(methodName) {
      var func = Math[methodName];
      return function(number, precision) {
        number = toNumber(number);
        precision = precision == null ? 0 : nativeMin(toInteger(precision), 292);
        if (precision && nativeIsFinite(number)) {
          // Shift with exponential notation to avoid floating-point issues.
          // See [MDN](https://mdn.io/round#Examples) for more details.
          var pair = (toString(number) + 'e').split('e'),
              value = func(pair[0] + 'e' + (+pair[1] + precision));

          pair = (toString(value) + 'e').split('e');
          return +(pair[0] + 'e' + (+pair[1] - precision));
        }
        return func(number);
      };
    }

    /**
     * Creates a set object of `values`.
     *
     * @private
     * @param {Array} values The values to add to the set.
     * @returns {Object} Returns the new set.
     */
    var createSet = !(Set && (1 / setToArray(new Set([,-0]))[1]) == INFINITY) ? noop : function(values) {
      return new Set(values);
    };

    /**
     * Creates a `_.toPairs` or `_.toPairsIn` function.
     *
     * @private
     * @param {Function} keysFunc The function to get the keys of a given object.
     * @returns {Function} Returns the new pairs function.
     */
    function createToPairs(keysFunc) {
      return function(object) {
        var tag = getTag(object);
        if (tag == mapTag) {
          return mapToArray(object);
        }
        if (tag == setTag) {
          return setToPairs(object);
        }
        return baseToPairs(object, keysFunc(object));
      };
    }

    /**
     * Creates a function that either curries or invokes `func` with optional
     * `this` binding and partially applied arguments.
     *
     * @private
     * @param {Function|string} func The function or method name to wrap.
     * @param {number} bitmask The bitmask flags.
     *    1 - `_.bind`
     *    2 - `_.bindKey`
     *    4 - `_.curry` or `_.curryRight` of a bound function
     *    8 - `_.curry`
     *   16 - `_.curryRight`
     *   32 - `_.partial`
     *   64 - `_.partialRight`
     *  128 - `_.rearg`
     *  256 - `_.ary`
     *  512 - `_.flip`
     * @param {*} [thisArg] The `this` binding of `func`.
     * @param {Array} [partials] The arguments to be partially applied.
     * @param {Array} [holders] The `partials` placeholder indexes.
     * @param {Array} [argPos] The argument positions of the new function.
     * @param {number} [ary] The arity cap of `func`.
     * @param {number} [arity] The arity of `func`.
     * @returns {Function} Returns the new wrapped function.
     */
    function createWrap(func, bitmask, thisArg, partials, holders, argPos, ary, arity) {
      var isBindKey = bitmask & WRAP_BIND_KEY_FLAG;
      if (!isBindKey && typeof func != 'function') {
        throw new TypeError(FUNC_ERROR_TEXT);
      }
      var length = partials ? partials.length : 0;
      if (!length) {
        bitmask &= ~(WRAP_PARTIAL_FLAG | WRAP_PARTIAL_RIGHT_FLAG);
        partials = holders = undefined;
      }
      ary = ary === undefined ? ary : nativeMax(toInteger(ary), 0);
      arity = arity === undefined ? arity : toInteger(arity);
      length -= holders ? holders.length : 0;

      if (bitmask & WRAP_PARTIAL_RIGHT_FLAG) {
        var partialsRight = partials,
            holdersRight = holders;

        partials = holders = undefined;
      }
      var data = isBindKey ? undefined : getData(func);

      var newData = [
        func, bitmask, thisArg, partials, holders, partialsRight, holdersRight,
        argPos, ary, arity
      ];

      if (data) {
        mergeData(newData, data);
      }
      func = newData[0];
      bitmask = newData[1];
      thisArg = newData[2];
      partials = newData[3];
      holders = newData[4];
      arity = newData[9] = newData[9] === undefined
        ? (isBindKey ? 0 : func.length)
        : nativeMax(newData[9] - length, 0);

      if (!arity && bitmask & (WRAP_CURRY_FLAG | WRAP_CURRY_RIGHT_FLAG)) {
        bitmask &= ~(WRAP_CURRY_FLAG | WRAP_CURRY_RIGHT_FLAG);
      }
      if (!bitmask || bitmask == WRAP_BIND_FLAG) {
        var result = createBind(func, bitmask, thisArg);
      } else if (bitmask == WRAP_CURRY_FLAG || bitmask == WRAP_CURRY_RIGHT_FLAG) {
        result = createCurry(func, bitmask, arity);
      } else if ((bitmask == WRAP_PARTIAL_FLAG || bitmask == (WRAP_BIND_FLAG | WRAP_PARTIAL_FLAG)) && !holders.length) {
        result = createPartial(func, bitmask, thisArg, partials);
      } else {
        result = createHybrid.apply(undefined, newData);
      }
      var setter = data ? baseSetData : setData;
      return setWrapToString(setter(result, newData), func, bitmask);
    }

    /**
     * Used by `_.defaults` to customize its `_.assignIn` use to assign properties
     * of source objects to the destination object for all destination properties
     * that resolve to `undefined`.
     *
     * @private
     * @param {*} objValue The destination value.
     * @param {*} srcValue The source value.
     * @param {string} key The key of the property to assign.
     * @param {Object} object The parent object of `objValue`.
     * @returns {*} Returns the value to assign.
     */
    function customDefaultsAssignIn(objValue, srcValue, key, object) {
      if (objValue === undefined ||
          (eq(objValue, objectProto[key]) && !hasOwnProperty.call(object, key))) {
        return srcValue;
      }
      return objValue;
    }

    /**
     * Used by `_.defaultsDeep` to customize its `_.merge` use to merge source
     * objects into destination objects that are passed thru.
     *
     * @private
     * @param {*} objValue The destination value.
     * @param {*} srcValue The source value.
     * @param {string} key The key of the property to merge.
     * @param {Object} object The parent object of `objValue`.
     * @param {Object} source The parent object of `srcValue`.
     * @param {Object} [stack] Tracks traversed source values and their merged
     *  counterparts.
     * @returns {*} Returns the value to assign.
     */
    function customDefaultsMerge(objValue, srcValue, key, object, source, stack) {
      if (isObject(objValue) && isObject(srcValue)) {
        // Recursively merge objects and arrays (susceptible to call stack limits).
        stack.set(srcValue, objValue);
        baseMerge(objValue, srcValue, undefined, customDefaultsMerge, stack);
        stack['delete'](srcValue);
      }
      return objValue;
    }

    /**
     * Used by `_.omit` to customize its `_.cloneDeep` use to only clone plain
     * objects.
     *
     * @private
     * @param {*} value The value to inspect.
     * @param {string} key The key of the property to inspect.
     * @returns {*} Returns the uncloned value or `undefined` to defer cloning to `_.cloneDeep`.
     */
    function customOmitClone(value) {
      return isPlainObject(value) ? undefined : value;
    }

    /**
     * A specialized version of `baseIsEqualDeep` for arrays with support for
     * partial deep comparisons.
     *
     * @private
     * @param {Array} array The array to compare.
     * @param {Array} other The other array to compare.
     * @param {number} bitmask The bitmask flags. See `baseIsEqual` for more details.
     * @param {Function} customizer The function to customize comparisons.
     * @param {Function} equalFunc The function to determine equivalents of values.
     * @param {Object} stack Tracks traversed `array` and `other` objects.
     * @returns {boolean} Returns `true` if the arrays are equivalent, else `false`.
     */
    function equalArrays(array, other, bitmask, customizer, equalFunc, stack) {
      var isPartial = bitmask & COMPARE_PARTIAL_FLAG,
          arrLength = array.length,
          othLength = other.length;

      if (arrLength != othLength && !(isPartial && othLength > arrLength)) {
        return false;
      }
      // Check that cyclic values are equal.
      var arrStacked = stack.get(array);
      var othStacked = stack.get(other);
      if (arrStacked && othStacked) {
        return arrStacked == other && othStacked == array;
      }
      var index = -1,
          result = true,
          seen = (bitmask & COMPARE_UNORDERED_FLAG) ? new SetCache : undefined;

      stack.set(array, other);
      stack.set(other, array);

      // Ignore non-index properties.
      while (++index < arrLength) {
        var arrValue = array[index],
            othValue = other[index];

        if (customizer) {
          var compared = isPartial
            ? customizer(othValue, arrValue, index, other, array, stack)
            : customizer(arrValue, othValue, index, array, other, stack);
        }
        if (compared !== undefined) {
          if (compared) {
            continue;
          }
          result = false;
          break;
        }
        // Recursively compare arrays (susceptible to call stack limits).
        if (seen) {
          if (!arraySome(other, function(othValue, othIndex) {
                if (!cacheHas(seen, othIndex) &&
                    (arrValue === othValue || equalFunc(arrValue, othValue, bitmask, customizer, stack))) {
                  return seen.push(othIndex);
                }
              })) {
            result = false;
            break;
          }
        } else if (!(
              arrValue === othValue ||
                equalFunc(arrValue, othValue, bitmask, customizer, stack)
            )) {
          result = false;
          break;
        }
      }
      stack['delete'](array);
      stack['delete'](other);
      return result;
    }

    /**
     * A specialized version of `baseIsEqualDeep` for comparing objects of
     * the same `toStringTag`.
     *
     * **Note:** This function only supports comparing values with tags of
     * `Boolean`, `Date`, `Error`, `Number`, `RegExp`, or `String`.
     *
     * @private
     * @param {Object} object The object to compare.
     * @param {Object} other The other object to compare.
     * @param {string} tag The `toStringTag` of the objects to compare.
     * @param {number} bitmask The bitmask flags. See `baseIsEqual` for more details.
     * @param {Function} customizer The function to customize comparisons.
     * @param {Function} equalFunc The function to determine equivalents of values.
     * @param {Object} stack Tracks traversed `object` and `other` objects.
     * @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
     */
    function equalByTag(object, other, tag, bitmask, customizer, equalFunc, stack) {
      switch (tag) {
        case dataViewTag:
          if ((object.byteLength != other.byteLength) ||
              (object.byteOffset != other.byteOffset)) {
            return false;
          }
          object = object.buffer;
          other = other.buffer;

        case arrayBufferTag:
          if ((object.byteLength != other.byteLength) ||
              !equalFunc(new Uint8Array(object), new Uint8Array(other))) {
            return false;
          }
          return true;

        case boolTag:
        case dateTag:
        case numberTag:
          // Coerce booleans to `1` or `0` and dates to milliseconds.
          // Invalid dates are coerced to `NaN`.
          return eq(+object, +other);

        case errorTag:
          return object.name == other.name && object.message == other.message;

        case regexpTag:
        case stringTag:
          // Coerce regexes to strings and treat strings, primitives and objects,
          // as equal. See http://www.ecma-international.org/ecma-262/7.0/#sec-regexp.prototype.tostring
          // for more details.
          return object == (other + '');

        case mapTag:
          var convert = mapToArray;

        case setTag:
          var isPartial = bitmask & COMPARE_PARTIAL_FLAG;
          convert || (convert = setToArray);

          if (object.size != other.size && !isPartial) {
            return false;
          }
          // Assume cyclic values are equal.
          var stacked = stack.get(object);
          if (stacked) {
            return stacked == other;
          }
          bitmask |= COMPARE_UNORDERED_FLAG;

          // Recursively compare objects (susceptible to call stack limits).
          stack.set(object, other);
          var result = equalArrays(convert(object), convert(other), bitmask, customizer, equalFunc, stack);
          stack['delete'](object);
          return result;

        case symbolTag:
          if (symbolValueOf) {
            return symbolValueOf.call(object) == symbolValueOf.call(other);
          }
      }
      return false;
    }

    /**
     * A specialized version of `baseIsEqualDeep` for objects with support for
     * partial deep comparisons.
     *
     * @private
     * @param {Object} object The object to compare.
     * @param {Object} other The other object to compare.
     * @param {number} bitmask The bitmask flags. See `baseIsEqual` for more details.
     * @param {Function} customizer The function to customize comparisons.
     * @param {Function} equalFunc The function to determine equivalents of values.
     * @param {Object} stack Tracks traversed `object` and `other` objects.
     * @returns {boolean} Returns `true` if the objects are equivalent, else `false`.
     */
    function equalObjects(object, other, bitmask, customizer, equalFunc, stack) {
      var isPartial = bitmask & COMPARE_PARTIAL_FLAG,
          objProps = getAllKeys(object),
          objLength = objProps.length,
          othProps = getAllKeys(other),
          othLength = othProps.length;

      if (objLength != othLength && !isPartial) {
        return false;
      }
      var index = objLength;
      while (index--) {
        var key = objProps[index];
        if (!(isPartial ? key in other : hasOwnProperty.call(other, key))) {
          return false;
        }
      }
      // Check that cyclic values are equal.
      var objStacked = stack.get(object);
      var othStacked = stack.get(other);
      if (objStacked && othStacked) {
        return objStacked == other && othStacked == object;
      }
      var result = true;
      stack.set(object, other);
      stack.set(other, object);

      var skipCtor = isPartial;
      while (++index < objLength) {
        key = objProps[index];
        var objValue = object[key],
            othValue = other[key];

        if (customizer) {
          var compared = isPartial
            ? customizer(othValue, objValue, key, other, object, stack)
            : customizer(objValue, othValue, key, object, other, stack);
        }
        // Recursively compare objects (susceptible to call stack limits).
        if (!(compared === undefined
              ? (objValue === othValue || equalFunc(objValue, othValue, bitmask, customizer, stack))
              : compared
            )) {
          result = false;
          break;
        }
        skipCtor || (skipCtor = key == 'constructor');
      }
      if (result && !skipCtor) {
        var objCtor = object.constructor,
            othCtor = other.constructor;

        // Non `Object` object instances with different constructors are not equal.
        if (objCtor != othCtor &&
            ('constructor' in object && 'constructor' in other) &&
            !(typeof objCtor == 'function' && objCtor instanceof objCtor &&
              typeof othCtor == 'function' && othCtor instanceof othCtor)) {
          result = false;
        }
      }
      stack['delete'](object);
      stack['delete'](other);
      return result;
    }

    /**
     * A specialized version of `baseRest` which flattens the rest array.
     *
     * @private
     * @param {Function} func The function to apply a rest parameter to.
     * @returns {Function} Returns the new function.
     */
    function flatRest(func) {
      return setToString(overRest(func, undefined, flatten), func + '');
    }

    /**
     * Creates an array of own enumerable property names and symbols of `object`.
     *
     * @private
     * @param {Object} object The object to query.
     * @returns {Array} Returns the array of property names and symbols.
     */
    function getAllKeys(object) {
      return baseGetAllKeys(object, keys, getSymbols);
    }

    /**
     * Creates an array of own and inherited enumerable property names and
     * symbols of `object`.
     *
     * @private
     * @param {Object} object The object to query.
     * @returns {Array} Returns the array of property names and symbols.
     */
    function getAllKeysIn(object) {
      return baseGetAllKeys(object, keysIn, getSymbolsIn);
    }

    /**
     * Gets metadata for `func`.
     *
     * @private
     * @param {Function} func The function to query.
     * @returns {*} Returns the metadata for `func`.
     */
    var getData = !metaMap ? noop : function(func) {
      return metaMap.get(func);
    };

    /**
     * Gets the name of `func`.
     *
     * @private
     * @param {Function} func The function to query.
     * @returns {string} Returns the function name.
     */
    function getFuncName(func) {
      var result = (func.name + ''),
          array = realNames[result],
          length = hasOwnProperty.call(realNames, result) ? array.length : 0;

      while (length--) {
        var data = array[length],
            otherFunc = data.func;
        if (otherFunc == null || otherFunc == func) {
          return data.name;
        }
      }
      return result;
    }

    /**
     * Gets the argument placeholder value for `func`.
     *
     * @private
     * @param {Function} func The function to inspect.
     * @returns {*} Returns the placeholder value.
     */
    function getHolder(func) {
      var object = hasOwnProperty.call(lodash, 'placeholder') ? lodash : func;
      return object.placeholder;
    }

    /**
     * Gets the appropriate "iteratee" function. If `_.iteratee` is customized,
     * this function returns the custom method, otherwise it returns `baseIteratee`.
     * If arguments are provided, the chosen function is invoked with them and
     * its result is returned.
     *
     * @private
     * @param {*} [value] The value to convert to an iteratee.
     * @param {number} [arity] The arity of the created iteratee.
     * @returns {Function} Returns the chosen function or its result.
     */
    function getIteratee() {
      var result = lodash.iteratee || iteratee;
      result = result === iteratee ? baseIteratee : result;
      return arguments.length ? result(arguments[0], arguments[1]) : result;
    }

    /**
     * Gets the data for `map`.
     *
     * @private
     * @param {Object} map The map to query.
     * @param {string} key The reference key.
     * @returns {*} Returns the map data.
     */
    function getMapData(map, key) {
      var data = map.__data__;
      return isKeyable(key)
        ? data[typeof key == 'string' ? 'string' : 'hash']
        : data.map;
    }

    /**
     * Gets the property names, values, and compare flags of `object`.
     *
     * @private
     * @param {Object} object The object to query.
     * @returns {Array} Returns the match data of `object`.
     */
    function getMatchData(object) {
      var result = keys(object),
          length = result.length;

      while (length--) {
        var key = result[length],
            value = object[key];

        result[length] = [key, value, isStrictComparable(value)];
      }
      return result;
    }

    /**
     * Gets the native function at `key` of `object`.
     *
     * @private
     * @param {Object} object The object to query.
     * @param {string} key The key of the method to get.
     * @returns {*} Returns the function if it's native, else `undefined`.
     */
    function getNative(object, key) {
      var value = getValue(object, key);
      return baseIsNative(value) ? value : undefined;
    }

    /**
     * A specialized version of `baseGetTag` which ignores `Symbol.toStringTag` values.
     *
     * @private
     * @param {*} value The value to query.
     * @returns {string} Returns the raw `toStringTag`.
     */
    function getRawTag(value) {
      var isOwn = hasOwnProperty.call(value, symToStringTag),
          tag = value[symToStringTag];

      try {
        value[symToStringTag] = undefined;
        var unmasked = true;
      } catch (e) {}

      var result = nativeObjectToString.call(value);
      if (unmasked) {
        if (isOwn) {
          value[symToStringTag] = tag;
        } else {
          delete value[symToStringTag];
        }
      }
      return result;
    }

    /**
     * Creates an array of the own enumerable symbols of `object`.
     *
     * @private
     * @param {Object} object The object to query.
     * @returns {Array} Returns the array of symbols.
     */
    var getSymbols = !nativeGetSymbols ? stubArray : function(object) {
      if (object == null) {
        return [];
      }
      object = Object(object);
      return arrayFilter(nativeGetSymbols(object), function(symbol) {
        return propertyIsEnumerable.call(object, symbol);
      });
    };

    /**
     * Creates an array of the own and inherited enumerable symbols of `object`.
     *
     * @private
     * @param {Object} object The object to query.
     * @returns {Array} Returns the array of symbols.
     */
    var getSymbolsIn = !nativeGetSymbols ? stubArray : function(object) {
      var result = [];
      while (object) {
        arrayPush(result, getSymbols(object));
        object = getPrototype(object);
      }
      return result;
    };

    /**
     * Gets the `toStringTag` of `value`.
     *
     * @private
     * @param {*} value The value to query.
     * @returns {string} Returns the `toStringTag`.
     */
    var getTag = baseGetTag;

    // Fallback for data views, maps, sets, and weak maps in IE 11 and promises in Node.js < 6.
    if ((DataView && getTag(new DataView(new ArrayBuffer(1))) != dataViewTag) ||
        (Map && getTag(new Map) != mapTag) ||
        (Promise && getTag(Promise.resolve()) != promiseTag) ||
        (Set && getTag(new Set) != setTag) ||
        (WeakMap && getTag(new WeakMap) != weakMapTag)) {
      getTag = function(value) {
        var result = baseGetTag(value),
            Ctor = result == objectTag ? value.constructor : undefined,
            ctorString = Ctor ? toSource(Ctor) : '';

        if (ctorString) {
          switch (ctorString) {
            case dataViewCtorString: return dataViewTag;
            case mapCtorString: return mapTag;
            case promiseCtorString: return promiseTag;
            case setCtorString: return setTag;
            case weakMapCtorString: return weakMapTag;
          }
        }
        return result;
      };
    }

    /**
     * Gets the view, applying any `transforms` to the `start` and `end` positions.
     *
     * @private
     * @param {number} start The start of the view.
     * @param {number} end The end of the view.
     * @param {Array} transforms The transformations to apply to the view.
     * @returns {Object} Returns an object containing the `start` and `end`
     *  positions of the view.
     */
    function getView(start, end, transforms) {
      var index = -1,
          length = transforms.length;

      while (++index < length) {
        var data = transforms[index],
            size = data.size;

        switch (data.type) {
          case 'drop':      start += size; break;
          case 'dropRight': end -= size; break;
          case 'take':      end = nativeMin(end, start + size); break;
          case 'takeRight': start = nativeMax(start, end - size); break;
        }
      }
      return { 'start': start, 'end': end };
    }

    /**
     * Extracts wrapper details from the `source` body comment.
     *
     * @private
     * @param {string} source The source to inspect.
     * @returns {Array} Returns the wrapper details.
     */
    function getWrapDetails(source) {
      var match = source.match(reWrapDetails);
      return match ? match[1].split(reSplitDetails) : [];
    }

    /**
     * Checks if `path` exists on `object`.
     *
     * @private
     * @param {Object} object The object to query.
     * @param {Array|string} path The path to check.
     * @param {Function} hasFunc The function to check properties.
     * @returns {boolean} Returns `true` if `path` exists, else `false`.
     */
    function hasPath(object, path, hasFunc) {
      path = castPath(path, object);

      var index = -1,
          length = path.length,
          result = false;

      while (++index < length) {
        var key = toKey(path[index]);
        if (!(result = object != null && hasFunc(object, key))) {
          break;
        }
        object = object[key];
      }
      if (result || ++index != length) {
        return result;
      }
      length = object == null ? 0 : object.length;
      return !!length && isLength(length) && isIndex(key, length) &&
        (isArray(object) || isArguments(object));
    }

    /**
     * Initializes an array clone.
     *
     * @private
     * @param {Array} array The array to clone.
     * @returns {Array} Returns the initialized clone.
     */
    function initCloneArray(array) {
      var length = array.length,
          result = new array.constructor(length);

      // Add properties assigned by `RegExp#exec`.
      if (length && typeof array[0] == 'string' && hasOwnProperty.call(array, 'index')) {
        result.index = array.index;
        result.input = array.input;
      }
      return result;
    }

    /**
     * Initializes an object clone.
     *
     * @private
     * @param {Object} object The object to clone.
     * @returns {Object} Returns the initialized clone.
     */
    function initCloneObject(object) {
      return (typeof object.constructor == 'function' && !isPrototype(object))
        ? baseCreate(getPrototype(object))
        : {};
    }

    /**
     * Initializes an object clone based on its `toStringTag`.
     *
     * **Note:** This function only supports cloning values with tags of
     * `Boolean`, `Date`, `Error`, `Map`, `Number`, `RegExp`, `Set`, or `String`.
     *
     * @private
     * @param {Object} object The object to clone.
     * @param {string} tag The `toStringTag` of the object to clone.
     * @param {boolean} [isDeep] Specify a deep clone.
     * @returns {Object} Returns the initialized clone.
     */
    function initCloneByTag(object, tag, isDeep) {
      var Ctor = object.constructor;
      switch (tag) {
        case arrayBufferTag:
          return cloneArrayBuffer(object);

        case boolTag:
        case dateTag:
          return new Ctor(+object);

        case dataViewTag:
          return cloneDataView(object, isDeep);

        case float32Tag: case float64Tag:
        case int8Tag: case int16Tag: case int32Tag:
        case uint8Tag: case uint8ClampedTag: case uint16Tag: case uint32Tag:
          return cloneTypedArray(object, isDeep);

        case mapTag:
          return new Ctor;

        case numberTag:
        case stringTag:
          return new Ctor(object);

        case regexpTag:
          return cloneRegExp(object);

        case setTag:
          return new Ctor;

        case symbolTag:
          return cloneSymbol(object);
      }
    }

    /**
     * Inserts wrapper `details` in a comment at the top of the `source` body.
     *
     * @private
     * @param {string} source The source to modify.
     * @returns {Array} details The details to insert.
     * @returns {string} Returns the modified source.
     */
    function insertWrapDetails(source, details) {
      var length = details.length;
      if (!length) {
        return source;
      }
      var lastIndex = length - 1;
      details[lastIndex] = (length > 1 ? '& ' : '') + details[lastIndex];
      details = details.join(length > 2 ? ', ' : ' ');
      return source.replace(reWrapComment, '{\n/* [wrapped with ' + details + '] */\n');
    }

    /**
     * Checks if `value` is a flattenable `arguments` object or array.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is flattenable, else `false`.
     */
    function isFlattenable(value) {
      return isArray(value) || isArguments(value) ||
        !!(spreadableSymbol && value && value[spreadableSymbol]);
    }

    /**
     * Checks if `value` is a valid array-like index.
     *
     * @private
     * @param {*} value The value to check.
     * @param {number} [length=MAX_SAFE_INTEGER] The upper bounds of a valid index.
     * @returns {boolean} Returns `true` if `value` is a valid index, else `false`.
     */
    function isIndex(value, length) {
      var type = typeof value;
      length = length == null ? MAX_SAFE_INTEGER : length;

      return !!length &&
        (type == 'number' ||
          (type != 'symbol' && reIsUint.test(value))) &&
            (value > -1 && value % 1 == 0 && value < length);
    }

    /**
     * Checks if the given arguments are from an iteratee call.
     *
     * @private
     * @param {*} value The potential iteratee value argument.
     * @param {*} index The potential iteratee index or key argument.
     * @param {*} object The potential iteratee object argument.
     * @returns {boolean} Returns `true` if the arguments are from an iteratee call,
     *  else `false`.
     */
    function isIterateeCall(value, index, object) {
      if (!isObject(object)) {
        return false;
      }
      var type = typeof index;
      if (type == 'number'
            ? (isArrayLike(object) && isIndex(index, object.length))
            : (type == 'string' && index in object)
          ) {
        return eq(object[index], value);
      }
      return false;
    }

    /**
     * Checks if `value` is a property name and not a property path.
     *
     * @private
     * @param {*} value The value to check.
     * @param {Object} [object] The object to query keys on.
     * @returns {boolean} Returns `true` if `value` is a property name, else `false`.
     */
    function isKey(value, object) {
      if (isArray(value)) {
        return false;
      }
      var type = typeof value;
      if (type == 'number' || type == 'symbol' || type == 'boolean' ||
          value == null || isSymbol(value)) {
        return true;
      }
      return reIsPlainProp.test(value) || !reIsDeepProp.test(value) ||
        (object != null && value in Object(object));
    }

    /**
     * Checks if `value` is suitable for use as unique object key.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is suitable, else `false`.
     */
    function isKeyable(value) {
      var type = typeof value;
      return (type == 'string' || type == 'number' || type == 'symbol' || type == 'boolean')
        ? (value !== '__proto__')
        : (value === null);
    }

    /**
     * Checks if `func` has a lazy counterpart.
     *
     * @private
     * @param {Function} func The function to check.
     * @returns {boolean} Returns `true` if `func` has a lazy counterpart,
     *  else `false`.
     */
    function isLaziable(func) {
      var funcName = getFuncName(func),
          other = lodash[funcName];

      if (typeof other != 'function' || !(funcName in LazyWrapper.prototype)) {
        return false;
      }
      if (func === other) {
        return true;
      }
      var data = getData(other);
      return !!data && func === data[0];
    }

    /**
     * Checks if `func` has its source masked.
     *
     * @private
     * @param {Function} func The function to check.
     * @returns {boolean} Returns `true` if `func` is masked, else `false`.
     */
    function isMasked(func) {
      return !!maskSrcKey && (maskSrcKey in func);
    }

    /**
     * Checks if `func` is capable of being masked.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `func` is maskable, else `false`.
     */
    var isMaskable = coreJsData ? isFunction : stubFalse;

    /**
     * Checks if `value` is likely a prototype object.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a prototype, else `false`.
     */
    function isPrototype(value) {
      var Ctor = value && value.constructor,
          proto = (typeof Ctor == 'function' && Ctor.prototype) || objectProto;

      return value === proto;
    }

    /**
     * Checks if `value` is suitable for strict equality comparisons, i.e. `===`.
     *
     * @private
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` if suitable for strict
     *  equality comparisons, else `false`.
     */
    function isStrictComparable(value) {
      return value === value && !isObject(value);
    }

    /**
     * A specialized version of `matchesProperty` for source values suitable
     * for strict equality comparisons, i.e. `===`.
     *
     * @private
     * @param {string} key The key of the property to get.
     * @param {*} srcValue The value to match.
     * @returns {Function} Returns the new spec function.
     */
    function matchesStrictComparable(key, srcValue) {
      return function(object) {
        if (object == null) {
          return false;
        }
        return object[key] === srcValue &&
          (srcValue !== undefined || (key in Object(object)));
      };
    }

    /**
     * A specialized version of `_.memoize` which clears the memoized function's
     * cache when it exceeds `MAX_MEMOIZE_SIZE`.
     *
     * @private
     * @param {Function} func The function to have its output memoized.
     * @returns {Function} Returns the new memoized function.
     */
    function memoizeCapped(func) {
      var result = memoize(func, function(key) {
        if (cache.size === MAX_MEMOIZE_SIZE) {
          cache.clear();
        }
        return key;
      });

      var cache = result.cache;
      return result;
    }

    /**
     * Merges the function metadata of `source` into `data`.
     *
     * Merging metadata reduces the number of wrappers used to invoke a function.
     * This is possible because methods like `_.bind`, `_.curry`, and `_.partial`
     * may be applied regardless of execution order. Methods like `_.ary` and
     * `_.rearg` modify function arguments, making the order in which they are
     * executed important, preventing the merging of metadata. However, we make
     * an exception for a safe combined case where curried functions have `_.ary`
     * and or `_.rearg` applied.
     *
     * @private
     * @param {Array} data The destination metadata.
     * @param {Array} source The source metadata.
     * @returns {Array} Returns `data`.
     */
    function mergeData(data, source) {
      var bitmask = data[1],
          srcBitmask = source[1],
          newBitmask = bitmask | srcBitmask,
          isCommon = newBitmask < (WRAP_BIND_FLAG | WRAP_BIND_KEY_FLAG | WRAP_ARY_FLAG);

      var isCombo =
        ((srcBitmask == WRAP_ARY_FLAG) && (bitmask == WRAP_CURRY_FLAG)) ||
        ((srcBitmask == WRAP_ARY_FLAG) && (bitmask == WRAP_REARG_FLAG) && (data[7].length <= source[8])) ||
        ((srcBitmask == (WRAP_ARY_FLAG | WRAP_REARG_FLAG)) && (source[7].length <= source[8]) && (bitmask == WRAP_CURRY_FLAG));

      // Exit early if metadata can't be merged.
      if (!(isCommon || isCombo)) {
        return data;
      }
      // Use source `thisArg` if available.
      if (srcBitmask & WRAP_BIND_FLAG) {
        data[2] = source[2];
        // Set when currying a bound function.
        newBitmask |= bitmask & WRAP_BIND_FLAG ? 0 : WRAP_CURRY_BOUND_FLAG;
      }
      // Compose partial arguments.
      var value = source[3];
      if (value) {
        var partials = data[3];
        data[3] = partials ? composeArgs(partials, value, source[4]) : value;
        data[4] = partials ? replaceHolders(data[3], PLACEHOLDER) : source[4];
      }
      // Compose partial right arguments.
      value = source[5];
      if (value) {
        partials = data[5];
        data[5] = partials ? composeArgsRight(partials, value, source[6]) : value;
        data[6] = partials ? replaceHolders(data[5], PLACEHOLDER) : source[6];
      }
      // Use source `argPos` if available.
      value = source[7];
      if (value) {
        data[7] = value;
      }
      // Use source `ary` if it's smaller.
      if (srcBitmask & WRAP_ARY_FLAG) {
        data[8] = data[8] == null ? source[8] : nativeMin(data[8], source[8]);
      }
      // Use source `arity` if one is not provided.
      if (data[9] == null) {
        data[9] = source[9];
      }
      // Use source `func` and merge bitmasks.
      data[0] = source[0];
      data[1] = newBitmask;

      return data;
    }

    /**
     * This function is like
     * [`Object.keys`](http://ecma-international.org/ecma-262/7.0/#sec-object.keys)
     * except that it includes inherited enumerable properties.
     *
     * @private
     * @param {Object} object The object to query.
     * @returns {Array} Returns the array of property names.
     */
    function nativeKeysIn(object) {
      var result = [];
      if (object != null) {
        for (var key in Object(object)) {
          result.push(key);
        }
      }
      return result;
    }

    /**
     * Converts `value` to a string using `Object.prototype.toString`.
     *
     * @private
     * @param {*} value The value to convert.
     * @returns {string} Returns the converted string.
     */
    function objectToString(value) {
      return nativeObjectToString.call(value);
    }

    /**
     * A specialized version of `baseRest` which transforms the rest array.
     *
     * @private
     * @param {Function} func The function to apply a rest parameter to.
     * @param {number} [start=func.length-1] The start position of the rest parameter.
     * @param {Function} transform The rest array transform.
     * @returns {Function} Returns the new function.
     */
    function overRest(func, start, transform) {
      start = nativeMax(start === undefined ? (func.length - 1) : start, 0);
      return function() {
        var args = arguments,
            index = -1,
            length = nativeMax(args.length - start, 0),
            array = Array(length);

        while (++index < length) {
          array[index] = args[start + index];
        }
        index = -1;
        var otherArgs = Array(start + 1);
        while (++index < start) {
          otherArgs[index] = args[index];
        }
        otherArgs[start] = transform(array);
        return apply(func, this, otherArgs);
      };
    }

    /**
     * Gets the parent value at `path` of `object`.
     *
     * @private
     * @param {Object} object The object to query.
     * @param {Array} path The path to get the parent value of.
     * @returns {*} Returns the parent value.
     */
    function parent(object, path) {
      return path.length < 2 ? object : baseGet(object, baseSlice(path, 0, -1));
    }

    /**
     * Reorder `array` according to the specified indexes where the element at
     * the first index is assigned as the first element, the element at
     * the second index is assigned as the second element, and so on.
     *
     * @private
     * @param {Array} array The array to reorder.
     * @param {Array} indexes The arranged array indexes.
     * @returns {Array} Returns `array`.
     */
    function reorder(array, indexes) {
      var arrLength = array.length,
          length = nativeMin(indexes.length, arrLength),
          oldArray = copyArray(array);

      while (length--) {
        var index = indexes[length];
        array[length] = isIndex(index, arrLength) ? oldArray[index] : undefined;
      }
      return array;
    }

    /**
     * Gets the value at `key`, unless `key` is "__proto__" or "constructor".
     *
     * @private
     * @param {Object} object The object to query.
     * @param {string} key The key of the property to get.
     * @returns {*} Returns the property value.
     */
    function safeGet(object, key) {
      if (key === 'constructor' && typeof object[key] === 'function') {
        return;
      }

      if (key == '__proto__') {
        return;
      }

      return object[key];
    }

    /**
     * Sets metadata for `func`.
     *
     * **Note:** If this function becomes hot, i.e. is invoked a lot in a short
     * period of time, it will trip its breaker and transition to an identity
     * function to avoid garbage collection pauses in V8. See
     * [V8 issue 2070](https://bugs.chromium.org/p/v8/issues/detail?id=2070)
     * for more details.
     *
     * @private
     * @param {Function} func The function to associate metadata with.
     * @param {*} data The metadata.
     * @returns {Function} Returns `func`.
     */
    var setData = shortOut(baseSetData);

    /**
     * A simple wrapper around the global [`setTimeout`](https://mdn.io/setTimeout).
     *
     * @private
     * @param {Function} func The function to delay.
     * @param {number} wait The number of milliseconds to delay invocation.
     * @returns {number|Object} Returns the timer id or timeout object.
     */
    var setTimeout = ctxSetTimeout || function(func, wait) {
      return root.setTimeout(func, wait);
    };

    /**
     * Sets the `toString` method of `func` to return `string`.
     *
     * @private
     * @param {Function} func The function to modify.
     * @param {Function} string The `toString` result.
     * @returns {Function} Returns `func`.
     */
    var setToString = shortOut(baseSetToString);

    /**
     * Sets the `toString` method of `wrapper` to mimic the source of `reference`
     * with wrapper details in a comment at the top of the source body.
     *
     * @private
     * @param {Function} wrapper The function to modify.
     * @param {Function} reference The reference function.
     * @param {number} bitmask The bitmask flags. See `createWrap` for more details.
     * @returns {Function} Returns `wrapper`.
     */
    function setWrapToString(wrapper, reference, bitmask) {
      var source = (reference + '');
      return setToString(wrapper, insertWrapDetails(source, updateWrapDetails(getWrapDetails(source), bitmask)));
    }

    /**
     * Creates a function that'll short out and invoke `identity` instead
     * of `func` when it's called `HOT_COUNT` or more times in `HOT_SPAN`
     * milliseconds.
     *
     * @private
     * @param {Function} func The function to restrict.
     * @returns {Function} Returns the new shortable function.
     */
    function shortOut(func) {
      var count = 0,
          lastCalled = 0;

      return function() {
        var stamp = nativeNow(),
            remaining = HOT_SPAN - (stamp - lastCalled);

        lastCalled = stamp;
        if (remaining > 0) {
          if (++count >= HOT_COUNT) {
            return arguments[0];
          }
        } else {
          count = 0;
        }
        return func.apply(undefined, arguments);
      };
    }

    /**
     * A specialized version of `_.shuffle` which mutates and sets the size of `array`.
     *
     * @private
     * @param {Array} array The array to shuffle.
     * @param {number} [size=array.length] The size of `array`.
     * @returns {Array} Returns `array`.
     */
    function shuffleSelf(array, size) {
      var index = -1,
          length = array.length,
          lastIndex = length - 1;

      size = size === undefined ? length : size;
      while (++index < size) {
        var rand = baseRandom(index, lastIndex),
            value = array[rand];

        array[rand] = array[index];
        array[index] = value;
      }
      array.length = size;
      return array;
    }

    /**
     * Converts `string` to a property path array.
     *
     * @private
     * @param {string} string The string to convert.
     * @returns {Array} Returns the property path array.
     */
    var stringToPath = memoizeCapped(function(string) {
      var result = [];
      if (string.charCodeAt(0) === 46 /* . */) {
        result.push('');
      }
      string.replace(rePropName, function(match, number, quote, subString) {
        result.push(quote ? subString.replace(reEscapeChar, '$1') : (number || match));
      });
      return result;
    });

    /**
     * Converts `value` to a string key if it's not a string or symbol.
     *
     * @private
     * @param {*} value The value to inspect.
     * @returns {string|symbol} Returns the key.
     */
    function toKey(value) {
      if (typeof value == 'string' || isSymbol(value)) {
        return value;
      }
      var result = (value + '');
      return (result == '0' && (1 / value) == -INFINITY) ? '-0' : result;
    }

    /**
     * Converts `func` to its source code.
     *
     * @private
     * @param {Function} func The function to convert.
     * @returns {string} Returns the source code.
     */
    function toSource(func) {
      if (func != null) {
        try {
          return funcToString.call(func);
        } catch (e) {}
        try {
          return (func + '');
        } catch (e) {}
      }
      return '';
    }

    /**
     * Updates wrapper `details` based on `bitmask` flags.
     *
     * @private
     * @returns {Array} details The details to modify.
     * @param {number} bitmask The bitmask flags. See `createWrap` for more details.
     * @returns {Array} Returns `details`.
     */
    function updateWrapDetails(details, bitmask) {
      arrayEach(wrapFlags, function(pair) {
        var value = '_.' + pair[0];
        if ((bitmask & pair[1]) && !arrayIncludes(details, value)) {
          details.push(value);
        }
      });
      return details.sort();
    }

    /**
     * Creates a clone of `wrapper`.
     *
     * @private
     * @param {Object} wrapper The wrapper to clone.
     * @returns {Object} Returns the cloned wrapper.
     */
    function wrapperClone(wrapper) {
      if (wrapper instanceof LazyWrapper) {
        return wrapper.clone();
      }
      var result = new LodashWrapper(wrapper.__wrapped__, wrapper.__chain__);
      result.__actions__ = copyArray(wrapper.__actions__);
      result.__index__  = wrapper.__index__;
      result.__values__ = wrapper.__values__;
      return result;
    }

    /*------------------------------------------------------------------------*/

    /**
     * Creates an array of elements split into groups the length of `size`.
     * If `array` can't be split evenly, the final chunk will be the remaining
     * elements.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Array
     * @param {Array} array The array to process.
     * @param {number} [size=1] The length of each chunk
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {Array} Returns the new array of chunks.
     * @example
     *
     * _.chunk(['a', 'b', 'c', 'd'], 2);
     * // => [['a', 'b'], ['c', 'd']]
     *
     * _.chunk(['a', 'b', 'c', 'd'], 3);
     * // => [['a', 'b', 'c'], ['d']]
     */
    function chunk(array, size, guard) {
      if ((guard ? isIterateeCall(array, size, guard) : size === undefined)) {
        size = 1;
      } else {
        size = nativeMax(toInteger(size), 0);
      }
      var length = array == null ? 0 : array.length;
      if (!length || size < 1) {
        return [];
      }
      var index = 0,
          resIndex = 0,
          result = Array(nativeCeil(length / size));

      while (index < length) {
        result[resIndex++] = baseSlice(array, index, (index += size));
      }
      return result;
    }

    /**
     * Creates an array with all falsey values removed. The values `false`, `null`,
     * `0`, `""`, `undefined`, and `NaN` are falsey.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Array
     * @param {Array} array The array to compact.
     * @returns {Array} Returns the new array of filtered values.
     * @example
     *
     * _.compact([0, 1, false, 2, '', 3]);
     * // => [1, 2, 3]
     */
    function compact(array) {
      var index = -1,
          length = array == null ? 0 : array.length,
          resIndex = 0,
          result = [];

      while (++index < length) {
        var value = array[index];
        if (value) {
          result[resIndex++] = value;
        }
      }
      return result;
    }

    /**
     * Creates a new array concatenating `array` with any additional arrays
     * and/or values.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The array to concatenate.
     * @param {...*} [values] The values to concatenate.
     * @returns {Array} Returns the new concatenated array.
     * @example
     *
     * var array = [1];
     * var other = _.concat(array, 2, [3], [[4]]);
     *
     * console.log(other);
     * // => [1, 2, 3, [4]]
     *
     * console.log(array);
     * // => [1]
     */
    function concat() {
      var length = arguments.length;
      if (!length) {
        return [];
      }
      var args = Array(length - 1),
          array = arguments[0],
          index = length;

      while (index--) {
        args[index - 1] = arguments[index];
      }
      return arrayPush(isArray(array) ? copyArray(array) : [array], baseFlatten(args, 1));
    }

    /**
     * Creates an array of `array` values not included in the other given arrays
     * using [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
     * for equality comparisons. The order and references of result values are
     * determined by the first array.
     *
     * **Note:** Unlike `_.pullAll`, this method returns a new array.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @param {...Array} [values] The values to exclude.
     * @returns {Array} Returns the new array of filtered values.
     * @see _.without, _.xor
     * @example
     *
     * _.difference([2, 1], [2, 3]);
     * // => [1]
     */
    var difference = baseRest(function(array, values) {
      return isArrayLikeObject(array)
        ? baseDifference(array, baseFlatten(values, 1, isArrayLikeObject, true))
        : [];
    });

    /**
     * This method is like `_.difference` except that it accepts `iteratee` which
     * is invoked for each element of `array` and `values` to generate the criterion
     * by which they're compared. The order and references of result values are
     * determined by the first array. The iteratee is invoked with one argument:
     * (value).
     *
     * **Note:** Unlike `_.pullAllBy`, this method returns a new array.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @param {...Array} [values] The values to exclude.
     * @param {Function} [iteratee=_.identity] The iteratee invoked per element.
     * @returns {Array} Returns the new array of filtered values.
     * @example
     *
     * _.differenceBy([2.1, 1.2], [2.3, 3.4], Math.floor);
     * // => [1.2]
     *
     * // The `_.property` iteratee shorthand.
     * _.differenceBy([{ 'x': 2 }, { 'x': 1 }], [{ 'x': 1 }], 'x');
     * // => [{ 'x': 2 }]
     */
    var differenceBy = baseRest(function(array, values) {
      var iteratee = last(values);
      if (isArrayLikeObject(iteratee)) {
        iteratee = undefined;
      }
      return isArrayLikeObject(array)
        ? baseDifference(array, baseFlatten(values, 1, isArrayLikeObject, true), getIteratee(iteratee, 2))
        : [];
    });

    /**
     * This method is like `_.difference` except that it accepts `comparator`
     * which is invoked to compare elements of `array` to `values`. The order and
     * references of result values are determined by the first array. The comparator
     * is invoked with two arguments: (arrVal, othVal).
     *
     * **Note:** Unlike `_.pullAllWith`, this method returns a new array.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @param {...Array} [values] The values to exclude.
     * @param {Function} [comparator] The comparator invoked per element.
     * @returns {Array} Returns the new array of filtered values.
     * @example
     *
     * var objects = [{ 'x': 1, 'y': 2 }, { 'x': 2, 'y': 1 }];
     *
     * _.differenceWith(objects, [{ 'x': 1, 'y': 2 }], _.isEqual);
     * // => [{ 'x': 2, 'y': 1 }]
     */
    var differenceWith = baseRest(function(array, values) {
      var comparator = last(values);
      if (isArrayLikeObject(comparator)) {
        comparator = undefined;
      }
      return isArrayLikeObject(array)
        ? baseDifference(array, baseFlatten(values, 1, isArrayLikeObject, true), undefined, comparator)
        : [];
    });

    /**
     * Creates a slice of `array` with `n` elements dropped from the beginning.
     *
     * @static
     * @memberOf _
     * @since 0.5.0
     * @category Array
     * @param {Array} array The array to query.
     * @param {number} [n=1] The number of elements to drop.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {Array} Returns the slice of `array`.
     * @example
     *
     * _.drop([1, 2, 3]);
     * // => [2, 3]
     *
     * _.drop([1, 2, 3], 2);
     * // => [3]
     *
     * _.drop([1, 2, 3], 5);
     * // => []
     *
     * _.drop([1, 2, 3], 0);
     * // => [1, 2, 3]
     */
    function drop(array, n, guard) {
      var length = array == null ? 0 : array.length;
      if (!length) {
        return [];
      }
      n = (guard || n === undefined) ? 1 : toInteger(n);
      return baseSlice(array, n < 0 ? 0 : n, length);
    }

    /**
     * Creates a slice of `array` with `n` elements dropped from the end.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Array
     * @param {Array} array The array to query.
     * @param {number} [n=1] The number of elements to drop.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {Array} Returns the slice of `array`.
     * @example
     *
     * _.dropRight([1, 2, 3]);
     * // => [1, 2]
     *
     * _.dropRight([1, 2, 3], 2);
     * // => [1]
     *
     * _.dropRight([1, 2, 3], 5);
     * // => []
     *
     * _.dropRight([1, 2, 3], 0);
     * // => [1, 2, 3]
     */
    function dropRight(array, n, guard) {
      var length = array == null ? 0 : array.length;
      if (!length) {
        return [];
      }
      n = (guard || n === undefined) ? 1 : toInteger(n);
      n = length - n;
      return baseSlice(array, 0, n < 0 ? 0 : n);
    }

    /**
     * Creates a slice of `array` excluding elements dropped from the end.
     * Elements are dropped until `predicate` returns falsey. The predicate is
     * invoked with three arguments: (value, index, array).
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Array
     * @param {Array} array The array to query.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @returns {Array} Returns the slice of `array`.
     * @example
     *
     * var users = [
     *   { 'user': 'barney',  'active': true },
     *   { 'user': 'fred',    'active': false },
     *   { 'user': 'pebbles', 'active': false }
     * ];
     *
     * _.dropRightWhile(users, function(o) { return !o.active; });
     * // => objects for ['barney']
     *
     * // The `_.matches` iteratee shorthand.
     * _.dropRightWhile(users, { 'user': 'pebbles', 'active': false });
     * // => objects for ['barney', 'fred']
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.dropRightWhile(users, ['active', false]);
     * // => objects for ['barney']
     *
     * // The `_.property` iteratee shorthand.
     * _.dropRightWhile(users, 'active');
     * // => objects for ['barney', 'fred', 'pebbles']
     */
    function dropRightWhile(array, predicate) {
      return (array && array.length)
        ? baseWhile(array, getIteratee(predicate, 3), true, true)
        : [];
    }

    /**
     * Creates a slice of `array` excluding elements dropped from the beginning.
     * Elements are dropped until `predicate` returns falsey. The predicate is
     * invoked with three arguments: (value, index, array).
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Array
     * @param {Array} array The array to query.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @returns {Array} Returns the slice of `array`.
     * @example
     *
     * var users = [
     *   { 'user': 'barney',  'active': false },
     *   { 'user': 'fred',    'active': false },
     *   { 'user': 'pebbles', 'active': true }
     * ];
     *
     * _.dropWhile(users, function(o) { return !o.active; });
     * // => objects for ['pebbles']
     *
     * // The `_.matches` iteratee shorthand.
     * _.dropWhile(users, { 'user': 'barney', 'active': false });
     * // => objects for ['fred', 'pebbles']
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.dropWhile(users, ['active', false]);
     * // => objects for ['pebbles']
     *
     * // The `_.property` iteratee shorthand.
     * _.dropWhile(users, 'active');
     * // => objects for ['barney', 'fred', 'pebbles']
     */
    function dropWhile(array, predicate) {
      return (array && array.length)
        ? baseWhile(array, getIteratee(predicate, 3), true)
        : [];
    }

    /**
     * Fills elements of `array` with `value` from `start` up to, but not
     * including, `end`.
     *
     * **Note:** This method mutates `array`.
     *
     * @static
     * @memberOf _
     * @since 3.2.0
     * @category Array
     * @param {Array} array The array to fill.
     * @param {*} value The value to fill `array` with.
     * @param {number} [start=0] The start position.
     * @param {number} [end=array.length] The end position.
     * @returns {Array} Returns `array`.
     * @example
     *
     * var array = [1, 2, 3];
     *
     * _.fill(array, 'a');
     * console.log(array);
     * // => ['a', 'a', 'a']
     *
     * _.fill(Array(3), 2);
     * // => [2, 2, 2]
     *
     * _.fill([4, 6, 8, 10], '*', 1, 3);
     * // => [4, '*', '*', 10]
     */
    function fill(array, value, start, end) {
      var length = array == null ? 0 : array.length;
      if (!length) {
        return [];
      }
      if (start && typeof start != 'number' && isIterateeCall(array, value, start)) {
        start = 0;
        end = length;
      }
      return baseFill(array, value, start, end);
    }

    /**
     * This method is like `_.find` except that it returns the index of the first
     * element `predicate` returns truthy for instead of the element itself.
     *
     * @static
     * @memberOf _
     * @since 1.1.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @param {number} [fromIndex=0] The index to search from.
     * @returns {number} Returns the index of the found element, else `-1`.
     * @example
     *
     * var users = [
     *   { 'user': 'barney',  'active': false },
     *   { 'user': 'fred',    'active': false },
     *   { 'user': 'pebbles', 'active': true }
     * ];
     *
     * _.findIndex(users, function(o) { return o.user == 'barney'; });
     * // => 0
     *
     * // The `_.matches` iteratee shorthand.
     * _.findIndex(users, { 'user': 'fred', 'active': false });
     * // => 1
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.findIndex(users, ['active', false]);
     * // => 0
     *
     * // The `_.property` iteratee shorthand.
     * _.findIndex(users, 'active');
     * // => 2
     */
    function findIndex(array, predicate, fromIndex) {
      var length = array == null ? 0 : array.length;
      if (!length) {
        return -1;
      }
      var index = fromIndex == null ? 0 : toInteger(fromIndex);
      if (index < 0) {
        index = nativeMax(length + index, 0);
      }
      return baseFindIndex(array, getIteratee(predicate, 3), index);
    }

    /**
     * This method is like `_.findIndex` except that it iterates over elements
     * of `collection` from right to left.
     *
     * @static
     * @memberOf _
     * @since 2.0.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @param {number} [fromIndex=array.length-1] The index to search from.
     * @returns {number} Returns the index of the found element, else `-1`.
     * @example
     *
     * var users = [
     *   { 'user': 'barney',  'active': true },
     *   { 'user': 'fred',    'active': false },
     *   { 'user': 'pebbles', 'active': false }
     * ];
     *
     * _.findLastIndex(users, function(o) { return o.user == 'pebbles'; });
     * // => 2
     *
     * // The `_.matches` iteratee shorthand.
     * _.findLastIndex(users, { 'user': 'barney', 'active': true });
     * // => 0
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.findLastIndex(users, ['active', false]);
     * // => 2
     *
     * // The `_.property` iteratee shorthand.
     * _.findLastIndex(users, 'active');
     * // => 0
     */
    function findLastIndex(array, predicate, fromIndex) {
      var length = array == null ? 0 : array.length;
      if (!length) {
        return -1;
      }
      var index = length - 1;
      if (fromIndex !== undefined) {
        index = toInteger(fromIndex);
        index = fromIndex < 0
          ? nativeMax(length + index, 0)
          : nativeMin(index, length - 1);
      }
      return baseFindIndex(array, getIteratee(predicate, 3), index, true);
    }

    /**
     * Flattens `array` a single level deep.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Array
     * @param {Array} array The array to flatten.
     * @returns {Array} Returns the new flattened array.
     * @example
     *
     * _.flatten([1, [2, [3, [4]], 5]]);
     * // => [1, 2, [3, [4]], 5]
     */
    function flatten(array) {
      var length = array == null ? 0 : array.length;
      return length ? baseFlatten(array, 1) : [];
    }

    /**
     * Recursively flattens `array`.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Array
     * @param {Array} array The array to flatten.
     * @returns {Array} Returns the new flattened array.
     * @example
     *
     * _.flattenDeep([1, [2, [3, [4]], 5]]);
     * // => [1, 2, 3, 4, 5]
     */
    function flattenDeep(array) {
      var length = array == null ? 0 : array.length;
      return length ? baseFlatten(array, INFINITY) : [];
    }

    /**
     * Recursively flatten `array` up to `depth` times.
     *
     * @static
     * @memberOf _
     * @since 4.4.0
     * @category Array
     * @param {Array} array The array to flatten.
     * @param {number} [depth=1] The maximum recursion depth.
     * @returns {Array} Returns the new flattened array.
     * @example
     *
     * var array = [1, [2, [3, [4]], 5]];
     *
     * _.flattenDepth(array, 1);
     * // => [1, 2, [3, [4]], 5]
     *
     * _.flattenDepth(array, 2);
     * // => [1, 2, 3, [4], 5]
     */
    function flattenDepth(array, depth) {
      var length = array == null ? 0 : array.length;
      if (!length) {
        return [];
      }
      depth = depth === undefined ? 1 : toInteger(depth);
      return baseFlatten(array, depth);
    }

    /**
     * The inverse of `_.toPairs`; this method returns an object composed
     * from key-value `pairs`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} pairs The key-value pairs.
     * @returns {Object} Returns the new object.
     * @example
     *
     * _.fromPairs([['a', 1], ['b', 2]]);
     * // => { 'a': 1, 'b': 2 }
     */
    function fromPairs(pairs) {
      var index = -1,
          length = pairs == null ? 0 : pairs.length,
          result = {};

      while (++index < length) {
        var pair = pairs[index];
        result[pair[0]] = pair[1];
      }
      return result;
    }

    /**
     * Gets the first element of `array`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @alias first
     * @category Array
     * @param {Array} array The array to query.
     * @returns {*} Returns the first element of `array`.
     * @example
     *
     * _.head([1, 2, 3]);
     * // => 1
     *
     * _.head([]);
     * // => undefined
     */
    function head(array) {
      return (array && array.length) ? array[0] : undefined;
    }

    /**
     * Gets the index at which the first occurrence of `value` is found in `array`
     * using [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
     * for equality comparisons. If `fromIndex` is negative, it's used as the
     * offset from the end of `array`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @param {*} value The value to search for.
     * @param {number} [fromIndex=0] The index to search from.
     * @returns {number} Returns the index of the matched value, else `-1`.
     * @example
     *
     * _.indexOf([1, 2, 1, 2], 2);
     * // => 1
     *
     * // Search from the `fromIndex`.
     * _.indexOf([1, 2, 1, 2], 2, 2);
     * // => 3
     */
    function indexOf(array, value, fromIndex) {
      var length = array == null ? 0 : array.length;
      if (!length) {
        return -1;
      }
      var index = fromIndex == null ? 0 : toInteger(fromIndex);
      if (index < 0) {
        index = nativeMax(length + index, 0);
      }
      return baseIndexOf(array, value, index);
    }

    /**
     * Gets all but the last element of `array`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Array
     * @param {Array} array The array to query.
     * @returns {Array} Returns the slice of `array`.
     * @example
     *
     * _.initial([1, 2, 3]);
     * // => [1, 2]
     */
    function initial(array) {
      var length = array == null ? 0 : array.length;
      return length ? baseSlice(array, 0, -1) : [];
    }

    /**
     * Creates an array of unique values that are included in all given arrays
     * using [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
     * for equality comparisons. The order and references of result values are
     * determined by the first array.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Array
     * @param {...Array} [arrays] The arrays to inspect.
     * @returns {Array} Returns the new array of intersecting values.
     * @example
     *
     * _.intersection([2, 1], [2, 3]);
     * // => [2]
     */
    var intersection = baseRest(function(arrays) {
      var mapped = arrayMap(arrays, castArrayLikeObject);
      return (mapped.length && mapped[0] === arrays[0])
        ? baseIntersection(mapped)
        : [];
    });

    /**
     * This method is like `_.intersection` except that it accepts `iteratee`
     * which is invoked for each element of each `arrays` to generate the criterion
     * by which they're compared. The order and references of result values are
     * determined by the first array. The iteratee is invoked with one argument:
     * (value).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {...Array} [arrays] The arrays to inspect.
     * @param {Function} [iteratee=_.identity] The iteratee invoked per element.
     * @returns {Array} Returns the new array of intersecting values.
     * @example
     *
     * _.intersectionBy([2.1, 1.2], [2.3, 3.4], Math.floor);
     * // => [2.1]
     *
     * // The `_.property` iteratee shorthand.
     * _.intersectionBy([{ 'x': 1 }], [{ 'x': 2 }, { 'x': 1 }], 'x');
     * // => [{ 'x': 1 }]
     */
    var intersectionBy = baseRest(function(arrays) {
      var iteratee = last(arrays),
          mapped = arrayMap(arrays, castArrayLikeObject);

      if (iteratee === last(mapped)) {
        iteratee = undefined;
      } else {
        mapped.pop();
      }
      return (mapped.length && mapped[0] === arrays[0])
        ? baseIntersection(mapped, getIteratee(iteratee, 2))
        : [];
    });

    /**
     * This method is like `_.intersection` except that it accepts `comparator`
     * which is invoked to compare elements of `arrays`. The order and references
     * of result values are determined by the first array. The comparator is
     * invoked with two arguments: (arrVal, othVal).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {...Array} [arrays] The arrays to inspect.
     * @param {Function} [comparator] The comparator invoked per element.
     * @returns {Array} Returns the new array of intersecting values.
     * @example
     *
     * var objects = [{ 'x': 1, 'y': 2 }, { 'x': 2, 'y': 1 }];
     * var others = [{ 'x': 1, 'y': 1 }, { 'x': 1, 'y': 2 }];
     *
     * _.intersectionWith(objects, others, _.isEqual);
     * // => [{ 'x': 1, 'y': 2 }]
     */
    var intersectionWith = baseRest(function(arrays) {
      var comparator = last(arrays),
          mapped = arrayMap(arrays, castArrayLikeObject);

      comparator = typeof comparator == 'function' ? comparator : undefined;
      if (comparator) {
        mapped.pop();
      }
      return (mapped.length && mapped[0] === arrays[0])
        ? baseIntersection(mapped, undefined, comparator)
        : [];
    });

    /**
     * Converts all elements in `array` into a string separated by `separator`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The array to convert.
     * @param {string} [separator=','] The element separator.
     * @returns {string} Returns the joined string.
     * @example
     *
     * _.join(['a', 'b', 'c'], '~');
     * // => 'a~b~c'
     */
    function join(array, separator) {
      return array == null ? '' : nativeJoin.call(array, separator);
    }

    /**
     * Gets the last element of `array`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Array
     * @param {Array} array The array to query.
     * @returns {*} Returns the last element of `array`.
     * @example
     *
     * _.last([1, 2, 3]);
     * // => 3
     */
    function last(array) {
      var length = array == null ? 0 : array.length;
      return length ? array[length - 1] : undefined;
    }

    /**
     * This method is like `_.indexOf` except that it iterates over elements of
     * `array` from right to left.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @param {*} value The value to search for.
     * @param {number} [fromIndex=array.length-1] The index to search from.
     * @returns {number} Returns the index of the matched value, else `-1`.
     * @example
     *
     * _.lastIndexOf([1, 2, 1, 2], 2);
     * // => 3
     *
     * // Search from the `fromIndex`.
     * _.lastIndexOf([1, 2, 1, 2], 2, 2);
     * // => 1
     */
    function lastIndexOf(array, value, fromIndex) {
      var length = array == null ? 0 : array.length;
      if (!length) {
        return -1;
      }
      var index = length;
      if (fromIndex !== undefined) {
        index = toInteger(fromIndex);
        index = index < 0 ? nativeMax(length + index, 0) : nativeMin(index, length - 1);
      }
      return value === value
        ? strictLastIndexOf(array, value, index)
        : baseFindIndex(array, baseIsNaN, index, true);
    }

    /**
     * Gets the element at index `n` of `array`. If `n` is negative, the nth
     * element from the end is returned.
     *
     * @static
     * @memberOf _
     * @since 4.11.0
     * @category Array
     * @param {Array} array The array to query.
     * @param {number} [n=0] The index of the element to return.
     * @returns {*} Returns the nth element of `array`.
     * @example
     *
     * var array = ['a', 'b', 'c', 'd'];
     *
     * _.nth(array, 1);
     * // => 'b'
     *
     * _.nth(array, -2);
     * // => 'c';
     */
    function nth(array, n) {
      return (array && array.length) ? baseNth(array, toInteger(n)) : undefined;
    }

    /**
     * Removes all given values from `array` using
     * [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
     * for equality comparisons.
     *
     * **Note:** Unlike `_.without`, this method mutates `array`. Use `_.remove`
     * to remove elements from an array by predicate.
     *
     * @static
     * @memberOf _
     * @since 2.0.0
     * @category Array
     * @param {Array} array The array to modify.
     * @param {...*} [values] The values to remove.
     * @returns {Array} Returns `array`.
     * @example
     *
     * var array = ['a', 'b', 'c', 'a', 'b', 'c'];
     *
     * _.pull(array, 'a', 'c');
     * console.log(array);
     * // => ['b', 'b']
     */
    var pull = baseRest(pullAll);

    /**
     * This method is like `_.pull` except that it accepts an array of values to remove.
     *
     * **Note:** Unlike `_.difference`, this method mutates `array`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The array to modify.
     * @param {Array} values The values to remove.
     * @returns {Array} Returns `array`.
     * @example
     *
     * var array = ['a', 'b', 'c', 'a', 'b', 'c'];
     *
     * _.pullAll(array, ['a', 'c']);
     * console.log(array);
     * // => ['b', 'b']
     */
    function pullAll(array, values) {
      return (array && array.length && values && values.length)
        ? basePullAll(array, values)
        : array;
    }

    /**
     * This method is like `_.pullAll` except that it accepts `iteratee` which is
     * invoked for each element of `array` and `values` to generate the criterion
     * by which they're compared. The iteratee is invoked with one argument: (value).
     *
     * **Note:** Unlike `_.differenceBy`, this method mutates `array`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The array to modify.
     * @param {Array} values The values to remove.
     * @param {Function} [iteratee=_.identity] The iteratee invoked per element.
     * @returns {Array} Returns `array`.
     * @example
     *
     * var array = [{ 'x': 1 }, { 'x': 2 }, { 'x': 3 }, { 'x': 1 }];
     *
     * _.pullAllBy(array, [{ 'x': 1 }, { 'x': 3 }], 'x');
     * console.log(array);
     * // => [{ 'x': 2 }]
     */
    function pullAllBy(array, values, iteratee) {
      return (array && array.length && values && values.length)
        ? basePullAll(array, values, getIteratee(iteratee, 2))
        : array;
    }

    /**
     * This method is like `_.pullAll` except that it accepts `comparator` which
     * is invoked to compare elements of `array` to `values`. The comparator is
     * invoked with two arguments: (arrVal, othVal).
     *
     * **Note:** Unlike `_.differenceWith`, this method mutates `array`.
     *
     * @static
     * @memberOf _
     * @since 4.6.0
     * @category Array
     * @param {Array} array The array to modify.
     * @param {Array} values The values to remove.
     * @param {Function} [comparator] The comparator invoked per element.
     * @returns {Array} Returns `array`.
     * @example
     *
     * var array = [{ 'x': 1, 'y': 2 }, { 'x': 3, 'y': 4 }, { 'x': 5, 'y': 6 }];
     *
     * _.pullAllWith(array, [{ 'x': 3, 'y': 4 }], _.isEqual);
     * console.log(array);
     * // => [{ 'x': 1, 'y': 2 }, { 'x': 5, 'y': 6 }]
     */
    function pullAllWith(array, values, comparator) {
      return (array && array.length && values && values.length)
        ? basePullAll(array, values, undefined, comparator)
        : array;
    }

    /**
     * Removes elements from `array` corresponding to `indexes` and returns an
     * array of removed elements.
     *
     * **Note:** Unlike `_.at`, this method mutates `array`.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Array
     * @param {Array} array The array to modify.
     * @param {...(number|number[])} [indexes] The indexes of elements to remove.
     * @returns {Array} Returns the new array of removed elements.
     * @example
     *
     * var array = ['a', 'b', 'c', 'd'];
     * var pulled = _.pullAt(array, [1, 3]);
     *
     * console.log(array);
     * // => ['a', 'c']
     *
     * console.log(pulled);
     * // => ['b', 'd']
     */
    var pullAt = flatRest(function(array, indexes) {
      var length = array == null ? 0 : array.length,
          result = baseAt(array, indexes);

      basePullAt(array, arrayMap(indexes, function(index) {
        return isIndex(index, length) ? +index : index;
      }).sort(compareAscending));

      return result;
    });

    /**
     * Removes all elements from `array` that `predicate` returns truthy for
     * and returns an array of the removed elements. The predicate is invoked
     * with three arguments: (value, index, array).
     *
     * **Note:** Unlike `_.filter`, this method mutates `array`. Use `_.pull`
     * to pull elements from an array by value.
     *
     * @static
     * @memberOf _
     * @since 2.0.0
     * @category Array
     * @param {Array} array The array to modify.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @returns {Array} Returns the new array of removed elements.
     * @example
     *
     * var array = [1, 2, 3, 4];
     * var evens = _.remove(array, function(n) {
     *   return n % 2 == 0;
     * });
     *
     * console.log(array);
     * // => [1, 3]
     *
     * console.log(evens);
     * // => [2, 4]
     */
    function remove(array, predicate) {
      var result = [];
      if (!(array && array.length)) {
        return result;
      }
      var index = -1,
          indexes = [],
          length = array.length;

      predicate = getIteratee(predicate, 3);
      while (++index < length) {
        var value = array[index];
        if (predicate(value, index, array)) {
          result.push(value);
          indexes.push(index);
        }
      }
      basePullAt(array, indexes);
      return result;
    }

    /**
     * Reverses `array` so that the first element becomes the last, the second
     * element becomes the second to last, and so on.
     *
     * **Note:** This method mutates `array` and is based on
     * [`Array#reverse`](https://mdn.io/Array/reverse).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The array to modify.
     * @returns {Array} Returns `array`.
     * @example
     *
     * var array = [1, 2, 3];
     *
     * _.reverse(array);
     * // => [3, 2, 1]
     *
     * console.log(array);
     * // => [3, 2, 1]
     */
    function reverse(array) {
      return array == null ? array : nativeReverse.call(array);
    }

    /**
     * Creates a slice of `array` from `start` up to, but not including, `end`.
     *
     * **Note:** This method is used instead of
     * [`Array#slice`](https://mdn.io/Array/slice) to ensure dense arrays are
     * returned.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Array
     * @param {Array} array The array to slice.
     * @param {number} [start=0] The start position.
     * @param {number} [end=array.length] The end position.
     * @returns {Array} Returns the slice of `array`.
     */
    function slice(array, start, end) {
      var length = array == null ? 0 : array.length;
      if (!length) {
        return [];
      }
      if (end && typeof end != 'number' && isIterateeCall(array, start, end)) {
        start = 0;
        end = length;
      }
      else {
        start = start == null ? 0 : toInteger(start);
        end = end === undefined ? length : toInteger(end);
      }
      return baseSlice(array, start, end);
    }

    /**
     * Uses a binary search to determine the lowest index at which `value`
     * should be inserted into `array` in order to maintain its sort order.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Array
     * @param {Array} array The sorted array to inspect.
     * @param {*} value The value to evaluate.
     * @returns {number} Returns the index at which `value` should be inserted
     *  into `array`.
     * @example
     *
     * _.sortedIndex([30, 50], 40);
     * // => 1
     */
    function sortedIndex(array, value) {
      return baseSortedIndex(array, value);
    }

    /**
     * This method is like `_.sortedIndex` except that it accepts `iteratee`
     * which is invoked for `value` and each element of `array` to compute their
     * sort ranking. The iteratee is invoked with one argument: (value).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The sorted array to inspect.
     * @param {*} value The value to evaluate.
     * @param {Function} [iteratee=_.identity] The iteratee invoked per element.
     * @returns {number} Returns the index at which `value` should be inserted
     *  into `array`.
     * @example
     *
     * var objects = [{ 'x': 4 }, { 'x': 5 }];
     *
     * _.sortedIndexBy(objects, { 'x': 4 }, function(o) { return o.x; });
     * // => 0
     *
     * // The `_.property` iteratee shorthand.
     * _.sortedIndexBy(objects, { 'x': 4 }, 'x');
     * // => 0
     */
    function sortedIndexBy(array, value, iteratee) {
      return baseSortedIndexBy(array, value, getIteratee(iteratee, 2));
    }

    /**
     * This method is like `_.indexOf` except that it performs a binary
     * search on a sorted `array`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @param {*} value The value to search for.
     * @returns {number} Returns the index of the matched value, else `-1`.
     * @example
     *
     * _.sortedIndexOf([4, 5, 5, 5, 6], 5);
     * // => 1
     */
    function sortedIndexOf(array, value) {
      var length = array == null ? 0 : array.length;
      if (length) {
        var index = baseSortedIndex(array, value);
        if (index < length && eq(array[index], value)) {
          return index;
        }
      }
      return -1;
    }

    /**
     * This method is like `_.sortedIndex` except that it returns the highest
     * index at which `value` should be inserted into `array` in order to
     * maintain its sort order.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Array
     * @param {Array} array The sorted array to inspect.
     * @param {*} value The value to evaluate.
     * @returns {number} Returns the index at which `value` should be inserted
     *  into `array`.
     * @example
     *
     * _.sortedLastIndex([4, 5, 5, 5, 6], 5);
     * // => 4
     */
    function sortedLastIndex(array, value) {
      return baseSortedIndex(array, value, true);
    }

    /**
     * This method is like `_.sortedLastIndex` except that it accepts `iteratee`
     * which is invoked for `value` and each element of `array` to compute their
     * sort ranking. The iteratee is invoked with one argument: (value).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The sorted array to inspect.
     * @param {*} value The value to evaluate.
     * @param {Function} [iteratee=_.identity] The iteratee invoked per element.
     * @returns {number} Returns the index at which `value` should be inserted
     *  into `array`.
     * @example
     *
     * var objects = [{ 'x': 4 }, { 'x': 5 }];
     *
     * _.sortedLastIndexBy(objects, { 'x': 4 }, function(o) { return o.x; });
     * // => 1
     *
     * // The `_.property` iteratee shorthand.
     * _.sortedLastIndexBy(objects, { 'x': 4 }, 'x');
     * // => 1
     */
    function sortedLastIndexBy(array, value, iteratee) {
      return baseSortedIndexBy(array, value, getIteratee(iteratee, 2), true);
    }

    /**
     * This method is like `_.lastIndexOf` except that it performs a binary
     * search on a sorted `array`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @param {*} value The value to search for.
     * @returns {number} Returns the index of the matched value, else `-1`.
     * @example
     *
     * _.sortedLastIndexOf([4, 5, 5, 5, 6], 5);
     * // => 3
     */
    function sortedLastIndexOf(array, value) {
      var length = array == null ? 0 : array.length;
      if (length) {
        var index = baseSortedIndex(array, value, true) - 1;
        if (eq(array[index], value)) {
          return index;
        }
      }
      return -1;
    }

    /**
     * This method is like `_.uniq` except that it's designed and optimized
     * for sorted arrays.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @returns {Array} Returns the new duplicate free array.
     * @example
     *
     * _.sortedUniq([1, 1, 2]);
     * // => [1, 2]
     */
    function sortedUniq(array) {
      return (array && array.length)
        ? baseSortedUniq(array)
        : [];
    }

    /**
     * This method is like `_.uniqBy` except that it's designed and optimized
     * for sorted arrays.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @param {Function} [iteratee] The iteratee invoked per element.
     * @returns {Array} Returns the new duplicate free array.
     * @example
     *
     * _.sortedUniqBy([1.1, 1.2, 2.3, 2.4], Math.floor);
     * // => [1.1, 2.3]
     */
    function sortedUniqBy(array, iteratee) {
      return (array && array.length)
        ? baseSortedUniq(array, getIteratee(iteratee, 2))
        : [];
    }

    /**
     * Gets all but the first element of `array`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The array to query.
     * @returns {Array} Returns the slice of `array`.
     * @example
     *
     * _.tail([1, 2, 3]);
     * // => [2, 3]
     */
    function tail(array) {
      var length = array == null ? 0 : array.length;
      return length ? baseSlice(array, 1, length) : [];
    }

    /**
     * Creates a slice of `array` with `n` elements taken from the beginning.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Array
     * @param {Array} array The array to query.
     * @param {number} [n=1] The number of elements to take.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {Array} Returns the slice of `array`.
     * @example
     *
     * _.take([1, 2, 3]);
     * // => [1]
     *
     * _.take([1, 2, 3], 2);
     * // => [1, 2]
     *
     * _.take([1, 2, 3], 5);
     * // => [1, 2, 3]
     *
     * _.take([1, 2, 3], 0);
     * // => []
     */
    function take(array, n, guard) {
      if (!(array && array.length)) {
        return [];
      }
      n = (guard || n === undefined) ? 1 : toInteger(n);
      return baseSlice(array, 0, n < 0 ? 0 : n);
    }

    /**
     * Creates a slice of `array` with `n` elements taken from the end.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Array
     * @param {Array} array The array to query.
     * @param {number} [n=1] The number of elements to take.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {Array} Returns the slice of `array`.
     * @example
     *
     * _.takeRight([1, 2, 3]);
     * // => [3]
     *
     * _.takeRight([1, 2, 3], 2);
     * // => [2, 3]
     *
     * _.takeRight([1, 2, 3], 5);
     * // => [1, 2, 3]
     *
     * _.takeRight([1, 2, 3], 0);
     * // => []
     */
    function takeRight(array, n, guard) {
      var length = array == null ? 0 : array.length;
      if (!length) {
        return [];
      }
      n = (guard || n === undefined) ? 1 : toInteger(n);
      n = length - n;
      return baseSlice(array, n < 0 ? 0 : n, length);
    }

    /**
     * Creates a slice of `array` with elements taken from the end. Elements are
     * taken until `predicate` returns falsey. The predicate is invoked with
     * three arguments: (value, index, array).
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Array
     * @param {Array} array The array to query.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @returns {Array} Returns the slice of `array`.
     * @example
     *
     * var users = [
     *   { 'user': 'barney',  'active': true },
     *   { 'user': 'fred',    'active': false },
     *   { 'user': 'pebbles', 'active': false }
     * ];
     *
     * _.takeRightWhile(users, function(o) { return !o.active; });
     * // => objects for ['fred', 'pebbles']
     *
     * // The `_.matches` iteratee shorthand.
     * _.takeRightWhile(users, { 'user': 'pebbles', 'active': false });
     * // => objects for ['pebbles']
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.takeRightWhile(users, ['active', false]);
     * // => objects for ['fred', 'pebbles']
     *
     * // The `_.property` iteratee shorthand.
     * _.takeRightWhile(users, 'active');
     * // => []
     */
    function takeRightWhile(array, predicate) {
      return (array && array.length)
        ? baseWhile(array, getIteratee(predicate, 3), false, true)
        : [];
    }

    /**
     * Creates a slice of `array` with elements taken from the beginning. Elements
     * are taken until `predicate` returns falsey. The predicate is invoked with
     * three arguments: (value, index, array).
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Array
     * @param {Array} array The array to query.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @returns {Array} Returns the slice of `array`.
     * @example
     *
     * var users = [
     *   { 'user': 'barney',  'active': false },
     *   { 'user': 'fred',    'active': false },
     *   { 'user': 'pebbles', 'active': true }
     * ];
     *
     * _.takeWhile(users, function(o) { return !o.active; });
     * // => objects for ['barney', 'fred']
     *
     * // The `_.matches` iteratee shorthand.
     * _.takeWhile(users, { 'user': 'barney', 'active': false });
     * // => objects for ['barney']
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.takeWhile(users, ['active', false]);
     * // => objects for ['barney', 'fred']
     *
     * // The `_.property` iteratee shorthand.
     * _.takeWhile(users, 'active');
     * // => []
     */
    function takeWhile(array, predicate) {
      return (array && array.length)
        ? baseWhile(array, getIteratee(predicate, 3))
        : [];
    }

    /**
     * Creates an array of unique values, in order, from all given arrays using
     * [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
     * for equality comparisons.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Array
     * @param {...Array} [arrays] The arrays to inspect.
     * @returns {Array} Returns the new array of combined values.
     * @example
     *
     * _.union([2], [1, 2]);
     * // => [2, 1]
     */
    var union = baseRest(function(arrays) {
      return baseUniq(baseFlatten(arrays, 1, isArrayLikeObject, true));
    });

    /**
     * This method is like `_.union` except that it accepts `iteratee` which is
     * invoked for each element of each `arrays` to generate the criterion by
     * which uniqueness is computed. Result values are chosen from the first
     * array in which the value occurs. The iteratee is invoked with one argument:
     * (value).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {...Array} [arrays] The arrays to inspect.
     * @param {Function} [iteratee=_.identity] The iteratee invoked per element.
     * @returns {Array} Returns the new array of combined values.
     * @example
     *
     * _.unionBy([2.1], [1.2, 2.3], Math.floor);
     * // => [2.1, 1.2]
     *
     * // The `_.property` iteratee shorthand.
     * _.unionBy([{ 'x': 1 }], [{ 'x': 2 }, { 'x': 1 }], 'x');
     * // => [{ 'x': 1 }, { 'x': 2 }]
     */
    var unionBy = baseRest(function(arrays) {
      var iteratee = last(arrays);
      if (isArrayLikeObject(iteratee)) {
        iteratee = undefined;
      }
      return baseUniq(baseFlatten(arrays, 1, isArrayLikeObject, true), getIteratee(iteratee, 2));
    });

    /**
     * This method is like `_.union` except that it accepts `comparator` which
     * is invoked to compare elements of `arrays`. Result values are chosen from
     * the first array in which the value occurs. The comparator is invoked
     * with two arguments: (arrVal, othVal).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {...Array} [arrays] The arrays to inspect.
     * @param {Function} [comparator] The comparator invoked per element.
     * @returns {Array} Returns the new array of combined values.
     * @example
     *
     * var objects = [{ 'x': 1, 'y': 2 }, { 'x': 2, 'y': 1 }];
     * var others = [{ 'x': 1, 'y': 1 }, { 'x': 1, 'y': 2 }];
     *
     * _.unionWith(objects, others, _.isEqual);
     * // => [{ 'x': 1, 'y': 2 }, { 'x': 2, 'y': 1 }, { 'x': 1, 'y': 1 }]
     */
    var unionWith = baseRest(function(arrays) {
      var comparator = last(arrays);
      comparator = typeof comparator == 'function' ? comparator : undefined;
      return baseUniq(baseFlatten(arrays, 1, isArrayLikeObject, true), undefined, comparator);
    });

    /**
     * Creates a duplicate-free version of an array, using
     * [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
     * for equality comparisons, in which only the first occurrence of each element
     * is kept. The order of result values is determined by the order they occur
     * in the array.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @returns {Array} Returns the new duplicate free array.
     * @example
     *
     * _.uniq([2, 1, 2]);
     * // => [2, 1]
     */
    function uniq(array) {
      return (array && array.length) ? baseUniq(array) : [];
    }

    /**
     * This method is like `_.uniq` except that it accepts `iteratee` which is
     * invoked for each element in `array` to generate the criterion by which
     * uniqueness is computed. The order of result values is determined by the
     * order they occur in the array. The iteratee is invoked with one argument:
     * (value).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @param {Function} [iteratee=_.identity] The iteratee invoked per element.
     * @returns {Array} Returns the new duplicate free array.
     * @example
     *
     * _.uniqBy([2.1, 1.2, 2.3], Math.floor);
     * // => [2.1, 1.2]
     *
     * // The `_.property` iteratee shorthand.
     * _.uniqBy([{ 'x': 1 }, { 'x': 2 }, { 'x': 1 }], 'x');
     * // => [{ 'x': 1 }, { 'x': 2 }]
     */
    function uniqBy(array, iteratee) {
      return (array && array.length) ? baseUniq(array, getIteratee(iteratee, 2)) : [];
    }

    /**
     * This method is like `_.uniq` except that it accepts `comparator` which
     * is invoked to compare elements of `array`. The order of result values is
     * determined by the order they occur in the array.The comparator is invoked
     * with two arguments: (arrVal, othVal).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @param {Function} [comparator] The comparator invoked per element.
     * @returns {Array} Returns the new duplicate free array.
     * @example
     *
     * var objects = [{ 'x': 1, 'y': 2 }, { 'x': 2, 'y': 1 }, { 'x': 1, 'y': 2 }];
     *
     * _.uniqWith(objects, _.isEqual);
     * // => [{ 'x': 1, 'y': 2 }, { 'x': 2, 'y': 1 }]
     */
    function uniqWith(array, comparator) {
      comparator = typeof comparator == 'function' ? comparator : undefined;
      return (array && array.length) ? baseUniq(array, undefined, comparator) : [];
    }

    /**
     * This method is like `_.zip` except that it accepts an array of grouped
     * elements and creates an array regrouping the elements to their pre-zip
     * configuration.
     *
     * @static
     * @memberOf _
     * @since 1.2.0
     * @category Array
     * @param {Array} array The array of grouped elements to process.
     * @returns {Array} Returns the new array of regrouped elements.
     * @example
     *
     * var zipped = _.zip(['a', 'b'], [1, 2], [true, false]);
     * // => [['a', 1, true], ['b', 2, false]]
     *
     * _.unzip(zipped);
     * // => [['a', 'b'], [1, 2], [true, false]]
     */
    function unzip(array) {
      if (!(array && array.length)) {
        return [];
      }
      var length = 0;
      array = arrayFilter(array, function(group) {
        if (isArrayLikeObject(group)) {
          length = nativeMax(group.length, length);
          return true;
        }
      });
      return baseTimes(length, function(index) {
        return arrayMap(array, baseProperty(index));
      });
    }

    /**
     * This method is like `_.unzip` except that it accepts `iteratee` to specify
     * how regrouped values should be combined. The iteratee is invoked with the
     * elements of each group: (...group).
     *
     * @static
     * @memberOf _
     * @since 3.8.0
     * @category Array
     * @param {Array} array The array of grouped elements to process.
     * @param {Function} [iteratee=_.identity] The function to combine
     *  regrouped values.
     * @returns {Array} Returns the new array of regrouped elements.
     * @example
     *
     * var zipped = _.zip([1, 2], [10, 20], [100, 200]);
     * // => [[1, 10, 100], [2, 20, 200]]
     *
     * _.unzipWith(zipped, _.add);
     * // => [3, 30, 300]
     */
    function unzipWith(array, iteratee) {
      if (!(array && array.length)) {
        return [];
      }
      var result = unzip(array);
      if (iteratee == null) {
        return result;
      }
      return arrayMap(result, function(group) {
        return apply(iteratee, undefined, group);
      });
    }

    /**
     * Creates an array excluding all given values using
     * [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
     * for equality comparisons.
     *
     * **Note:** Unlike `_.pull`, this method returns a new array.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Array
     * @param {Array} array The array to inspect.
     * @param {...*} [values] The values to exclude.
     * @returns {Array} Returns the new array of filtered values.
     * @see _.difference, _.xor
     * @example
     *
     * _.without([2, 1, 2, 3], 1, 2);
     * // => [3]
     */
    var without = baseRest(function(array, values) {
      return isArrayLikeObject(array)
        ? baseDifference(array, values)
        : [];
    });

    /**
     * Creates an array of unique values that is the
     * [symmetric difference](https://en.wikipedia.org/wiki/Symmetric_difference)
     * of the given arrays. The order of result values is determined by the order
     * they occur in the arrays.
     *
     * @static
     * @memberOf _
     * @since 2.4.0
     * @category Array
     * @param {...Array} [arrays] The arrays to inspect.
     * @returns {Array} Returns the new array of filtered values.
     * @see _.difference, _.without
     * @example
     *
     * _.xor([2, 1], [2, 3]);
     * // => [1, 3]
     */
    var xor = baseRest(function(arrays) {
      return baseXor(arrayFilter(arrays, isArrayLikeObject));
    });

    /**
     * This method is like `_.xor` except that it accepts `iteratee` which is
     * invoked for each element of each `arrays` to generate the criterion by
     * which by which they're compared. The order of result values is determined
     * by the order they occur in the arrays. The iteratee is invoked with one
     * argument: (value).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {...Array} [arrays] The arrays to inspect.
     * @param {Function} [iteratee=_.identity] The iteratee invoked per element.
     * @returns {Array} Returns the new array of filtered values.
     * @example
     *
     * _.xorBy([2.1, 1.2], [2.3, 3.4], Math.floor);
     * // => [1.2, 3.4]
     *
     * // The `_.property` iteratee shorthand.
     * _.xorBy([{ 'x': 1 }], [{ 'x': 2 }, { 'x': 1 }], 'x');
     * // => [{ 'x': 2 }]
     */
    var xorBy = baseRest(function(arrays) {
      var iteratee = last(arrays);
      if (isArrayLikeObject(iteratee)) {
        iteratee = undefined;
      }
      return baseXor(arrayFilter(arrays, isArrayLikeObject), getIteratee(iteratee, 2));
    });

    /**
     * This method is like `_.xor` except that it accepts `comparator` which is
     * invoked to compare elements of `arrays`. The order of result values is
     * determined by the order they occur in the arrays. The comparator is invoked
     * with two arguments: (arrVal, othVal).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Array
     * @param {...Array} [arrays] The arrays to inspect.
     * @param {Function} [comparator] The comparator invoked per element.
     * @returns {Array} Returns the new array of filtered values.
     * @example
     *
     * var objects = [{ 'x': 1, 'y': 2 }, { 'x': 2, 'y': 1 }];
     * var others = [{ 'x': 1, 'y': 1 }, { 'x': 1, 'y': 2 }];
     *
     * _.xorWith(objects, others, _.isEqual);
     * // => [{ 'x': 2, 'y': 1 }, { 'x': 1, 'y': 1 }]
     */
    var xorWith = baseRest(function(arrays) {
      var comparator = last(arrays);
      comparator = typeof comparator == 'function' ? comparator : undefined;
      return baseXor(arrayFilter(arrays, isArrayLikeObject), undefined, comparator);
    });

    /**
     * Creates an array of grouped elements, the first of which contains the
     * first elements of the given arrays, the second of which contains the
     * second elements of the given arrays, and so on.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Array
     * @param {...Array} [arrays] The arrays to process.
     * @returns {Array} Returns the new array of grouped elements.
     * @example
     *
     * _.zip(['a', 'b'], [1, 2], [true, false]);
     * // => [['a', 1, true], ['b', 2, false]]
     */
    var zip = baseRest(unzip);

    /**
     * This method is like `_.fromPairs` except that it accepts two arrays,
     * one of property identifiers and one of corresponding values.
     *
     * @static
     * @memberOf _
     * @since 0.4.0
     * @category Array
     * @param {Array} [props=[]] The property identifiers.
     * @param {Array} [values=[]] The property values.
     * @returns {Object} Returns the new object.
     * @example
     *
     * _.zipObject(['a', 'b'], [1, 2]);
     * // => { 'a': 1, 'b': 2 }
     */
    function zipObject(props, values) {
      return baseZipObject(props || [], values || [], assignValue);
    }

    /**
     * This method is like `_.zipObject` except that it supports property paths.
     *
     * @static
     * @memberOf _
     * @since 4.1.0
     * @category Array
     * @param {Array} [props=[]] The property identifiers.
     * @param {Array} [values=[]] The property values.
     * @returns {Object} Returns the new object.
     * @example
     *
     * _.zipObjectDeep(['a.b[0].c', 'a.b[1].d'], [1, 2]);
     * // => { 'a': { 'b': [{ 'c': 1 }, { 'd': 2 }] } }
     */
    function zipObjectDeep(props, values) {
      return baseZipObject(props || [], values || [], baseSet);
    }

    /**
     * This method is like `_.zip` except that it accepts `iteratee` to specify
     * how grouped values should be combined. The iteratee is invoked with the
     * elements of each group: (...group).
     *
     * @static
     * @memberOf _
     * @since 3.8.0
     * @category Array
     * @param {...Array} [arrays] The arrays to process.
     * @param {Function} [iteratee=_.identity] The function to combine
     *  grouped values.
     * @returns {Array} Returns the new array of grouped elements.
     * @example
     *
     * _.zipWith([1, 2], [10, 20], [100, 200], function(a, b, c) {
     *   return a + b + c;
     * });
     * // => [111, 222]
     */
    var zipWith = baseRest(function(arrays) {
      var length = arrays.length,
          iteratee = length > 1 ? arrays[length - 1] : undefined;

      iteratee = typeof iteratee == 'function' ? (arrays.pop(), iteratee) : undefined;
      return unzipWith(arrays, iteratee);
    });

    /*------------------------------------------------------------------------*/

    /**
     * Creates a `lodash` wrapper instance that wraps `value` with explicit method
     * chain sequences enabled. The result of such sequences must be unwrapped
     * with `_#value`.
     *
     * @static
     * @memberOf _
     * @since 1.3.0
     * @category Seq
     * @param {*} value The value to wrap.
     * @returns {Object} Returns the new `lodash` wrapper instance.
     * @example
     *
     * var users = [
     *   { 'user': 'barney',  'age': 36 },
     *   { 'user': 'fred',    'age': 40 },
     *   { 'user': 'pebbles', 'age': 1 }
     * ];
     *
     * var youngest = _
     *   .chain(users)
     *   .sortBy('age')
     *   .map(function(o) {
     *     return o.user + ' is ' + o.age;
     *   })
     *   .head()
     *   .value();
     * // => 'pebbles is 1'
     */
    function chain(value) {
      var result = lodash(value);
      result.__chain__ = true;
      return result;
    }

    /**
     * This method invokes `interceptor` and returns `value`. The interceptor
     * is invoked with one argument; (value). The purpose of this method is to
     * "tap into" a method chain sequence in order to modify intermediate results.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Seq
     * @param {*} value The value to provide to `interceptor`.
     * @param {Function} interceptor The function to invoke.
     * @returns {*} Returns `value`.
     * @example
     *
     * _([1, 2, 3])
     *  .tap(function(array) {
     *    // Mutate input array.
     *    array.pop();
     *  })
     *  .reverse()
     *  .value();
     * // => [2, 1]
     */
    function tap(value, interceptor) {
      interceptor(value);
      return value;
    }

    /**
     * This method is like `_.tap` except that it returns the result of `interceptor`.
     * The purpose of this method is to "pass thru" values replacing intermediate
     * results in a method chain sequence.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Seq
     * @param {*} value The value to provide to `interceptor`.
     * @param {Function} interceptor The function to invoke.
     * @returns {*} Returns the result of `interceptor`.
     * @example
     *
     * _('  abc  ')
     *  .chain()
     *  .trim()
     *  .thru(function(value) {
     *    return [value];
     *  })
     *  .value();
     * // => ['abc']
     */
    function thru(value, interceptor) {
      return interceptor(value);
    }

    /**
     * This method is the wrapper version of `_.at`.
     *
     * @name at
     * @memberOf _
     * @since 1.0.0
     * @category Seq
     * @param {...(string|string[])} [paths] The property paths to pick.
     * @returns {Object} Returns the new `lodash` wrapper instance.
     * @example
     *
     * var object = { 'a': [{ 'b': { 'c': 3 } }, 4] };
     *
     * _(object).at(['a[0].b.c', 'a[1]']).value();
     * // => [3, 4]
     */
    var wrapperAt = flatRest(function(paths) {
      var length = paths.length,
          start = length ? paths[0] : 0,
          value = this.__wrapped__,
          interceptor = function(object) { return baseAt(object, paths); };

      if (length > 1 || this.__actions__.length ||
          !(value instanceof LazyWrapper) || !isIndex(start)) {
        return this.thru(interceptor);
      }
      value = value.slice(start, +start + (length ? 1 : 0));
      value.__actions__.push({
        'func': thru,
        'args': [interceptor],
        'thisArg': undefined
      });
      return new LodashWrapper(value, this.__chain__).thru(function(array) {
        if (length && !array.length) {
          array.push(undefined);
        }
        return array;
      });
    });

    /**
     * Creates a `lodash` wrapper instance with explicit method chain sequences enabled.
     *
     * @name chain
     * @memberOf _
     * @since 0.1.0
     * @category Seq
     * @returns {Object} Returns the new `lodash` wrapper instance.
     * @example
     *
     * var users = [
     *   { 'user': 'barney', 'age': 36 },
     *   { 'user': 'fred',   'age': 40 }
     * ];
     *
     * // A sequence without explicit chaining.
     * _(users).head();
     * // => { 'user': 'barney', 'age': 36 }
     *
     * // A sequence with explicit chaining.
     * _(users)
     *   .chain()
     *   .head()
     *   .pick('user')
     *   .value();
     * // => { 'user': 'barney' }
     */
    function wrapperChain() {
      return chain(this);
    }

    /**
     * Executes the chain sequence and returns the wrapped result.
     *
     * @name commit
     * @memberOf _
     * @since 3.2.0
     * @category Seq
     * @returns {Object} Returns the new `lodash` wrapper instance.
     * @example
     *
     * var array = [1, 2];
     * var wrapped = _(array).push(3);
     *
     * console.log(array);
     * // => [1, 2]
     *
     * wrapped = wrapped.commit();
     * console.log(array);
     * // => [1, 2, 3]
     *
     * wrapped.last();
     * // => 3
     *
     * console.log(array);
     * // => [1, 2, 3]
     */
    function wrapperCommit() {
      return new LodashWrapper(this.value(), this.__chain__);
    }

    /**
     * Gets the next value on a wrapped object following the
     * [iterator protocol](https://mdn.io/iteration_protocols#iterator).
     *
     * @name next
     * @memberOf _
     * @since 4.0.0
     * @category Seq
     * @returns {Object} Returns the next iterator value.
     * @example
     *
     * var wrapped = _([1, 2]);
     *
     * wrapped.next();
     * // => { 'done': false, 'value': 1 }
     *
     * wrapped.next();
     * // => { 'done': false, 'value': 2 }
     *
     * wrapped.next();
     * // => { 'done': true, 'value': undefined }
     */
    function wrapperNext() {
      if (this.__values__ === undefined) {
        this.__values__ = toArray(this.value());
      }
      var done = this.__index__ >= this.__values__.length,
          value = done ? undefined : this.__values__[this.__index__++];

      return { 'done': done, 'value': value };
    }

    /**
     * Enables the wrapper to be iterable.
     *
     * @name Symbol.iterator
     * @memberOf _
     * @since 4.0.0
     * @category Seq
     * @returns {Object} Returns the wrapper object.
     * @example
     *
     * var wrapped = _([1, 2]);
     *
     * wrapped[Symbol.iterator]() === wrapped;
     * // => true
     *
     * Array.from(wrapped);
     * // => [1, 2]
     */
    function wrapperToIterator() {
      return this;
    }

    /**
     * Creates a clone of the chain sequence planting `value` as the wrapped value.
     *
     * @name plant
     * @memberOf _
     * @since 3.2.0
     * @category Seq
     * @param {*} value The value to plant.
     * @returns {Object} Returns the new `lodash` wrapper instance.
     * @example
     *
     * function square(n) {
     *   return n * n;
     * }
     *
     * var wrapped = _([1, 2]).map(square);
     * var other = wrapped.plant([3, 4]);
     *
     * other.value();
     * // => [9, 16]
     *
     * wrapped.value();
     * // => [1, 4]
     */
    function wrapperPlant(value) {
      var result,
          parent = this;

      while (parent instanceof baseLodash) {
        var clone = wrapperClone(parent);
        clone.__index__ = 0;
        clone.__values__ = undefined;
        if (result) {
          previous.__wrapped__ = clone;
        } else {
          result = clone;
        }
        var previous = clone;
        parent = parent.__wrapped__;
      }
      previous.__wrapped__ = value;
      return result;
    }

    /**
     * This method is the wrapper version of `_.reverse`.
     *
     * **Note:** This method mutates the wrapped array.
     *
     * @name reverse
     * @memberOf _
     * @since 0.1.0
     * @category Seq
     * @returns {Object} Returns the new `lodash` wrapper instance.
     * @example
     *
     * var array = [1, 2, 3];
     *
     * _(array).reverse().value()
     * // => [3, 2, 1]
     *
     * console.log(array);
     * // => [3, 2, 1]
     */
    function wrapperReverse() {
      var value = this.__wrapped__;
      if (value instanceof LazyWrapper) {
        var wrapped = value;
        if (this.__actions__.length) {
          wrapped = new LazyWrapper(this);
        }
        wrapped = wrapped.reverse();
        wrapped.__actions__.push({
          'func': thru,
          'args': [reverse],
          'thisArg': undefined
        });
        return new LodashWrapper(wrapped, this.__chain__);
      }
      return this.thru(reverse);
    }

    /**
     * Executes the chain sequence to resolve the unwrapped value.
     *
     * @name value
     * @memberOf _
     * @since 0.1.0
     * @alias toJSON, valueOf
     * @category Seq
     * @returns {*} Returns the resolved unwrapped value.
     * @example
     *
     * _([1, 2, 3]).value();
     * // => [1, 2, 3]
     */
    function wrapperValue() {
      return baseWrapperValue(this.__wrapped__, this.__actions__);
    }

    /*------------------------------------------------------------------------*/

    /**
     * Creates an object composed of keys generated from the results of running
     * each element of `collection` thru `iteratee`. The corresponding value of
     * each key is the number of times the key was returned by `iteratee`. The
     * iteratee is invoked with one argument: (value).
     *
     * @static
     * @memberOf _
     * @since 0.5.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [iteratee=_.identity] The iteratee to transform keys.
     * @returns {Object} Returns the composed aggregate object.
     * @example
     *
     * _.countBy([6.1, 4.2, 6.3], Math.floor);
     * // => { '4': 1, '6': 2 }
     *
     * // The `_.property` iteratee shorthand.
     * _.countBy(['one', 'two', 'three'], 'length');
     * // => { '3': 2, '5': 1 }
     */
    var countBy = createAggregator(function(result, value, key) {
      if (hasOwnProperty.call(result, key)) {
        ++result[key];
      } else {
        baseAssignValue(result, key, 1);
      }
    });

    /**
     * Checks if `predicate` returns truthy for **all** elements of `collection`.
     * Iteration is stopped once `predicate` returns falsey. The predicate is
     * invoked with three arguments: (value, index|key, collection).
     *
     * **Note:** This method returns `true` for
     * [empty collections](https://en.wikipedia.org/wiki/Empty_set) because
     * [everything is true](https://en.wikipedia.org/wiki/Vacuous_truth) of
     * elements of empty collections.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {boolean} Returns `true` if all elements pass the predicate check,
     *  else `false`.
     * @example
     *
     * _.every([true, 1, null, 'yes'], Boolean);
     * // => false
     *
     * var users = [
     *   { 'user': 'barney', 'age': 36, 'active': false },
     *   { 'user': 'fred',   'age': 40, 'active': false }
     * ];
     *
     * // The `_.matches` iteratee shorthand.
     * _.every(users, { 'user': 'barney', 'active': false });
     * // => false
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.every(users, ['active', false]);
     * // => true
     *
     * // The `_.property` iteratee shorthand.
     * _.every(users, 'active');
     * // => false
     */
    function every(collection, predicate, guard) {
      var func = isArray(collection) ? arrayEvery : baseEvery;
      if (guard && isIterateeCall(collection, predicate, guard)) {
        predicate = undefined;
      }
      return func(collection, getIteratee(predicate, 3));
    }

    /**
     * Iterates over elements of `collection`, returning an array of all elements
     * `predicate` returns truthy for. The predicate is invoked with three
     * arguments: (value, index|key, collection).
     *
     * **Note:** Unlike `_.remove`, this method returns a new array.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @returns {Array} Returns the new filtered array.
     * @see _.reject
     * @example
     *
     * var users = [
     *   { 'user': 'barney', 'age': 36, 'active': true },
     *   { 'user': 'fred',   'age': 40, 'active': false }
     * ];
     *
     * _.filter(users, function(o) { return !o.active; });
     * // => objects for ['fred']
     *
     * // The `_.matches` iteratee shorthand.
     * _.filter(users, { 'age': 36, 'active': true });
     * // => objects for ['barney']
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.filter(users, ['active', false]);
     * // => objects for ['fred']
     *
     * // The `_.property` iteratee shorthand.
     * _.filter(users, 'active');
     * // => objects for ['barney']
     *
     * // Combining several predicates using `_.overEvery` or `_.overSome`.
     * _.filter(users, _.overSome([{ 'age': 36 }, ['age', 40]]));
     * // => objects for ['fred', 'barney']
     */
    function filter(collection, predicate) {
      var func = isArray(collection) ? arrayFilter : baseFilter;
      return func(collection, getIteratee(predicate, 3));
    }

    /**
     * Iterates over elements of `collection`, returning the first element
     * `predicate` returns truthy for. The predicate is invoked with three
     * arguments: (value, index|key, collection).
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Collection
     * @param {Array|Object} collection The collection to inspect.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @param {number} [fromIndex=0] The index to search from.
     * @returns {*} Returns the matched element, else `undefined`.
     * @example
     *
     * var users = [
     *   { 'user': 'barney',  'age': 36, 'active': true },
     *   { 'user': 'fred',    'age': 40, 'active': false },
     *   { 'user': 'pebbles', 'age': 1,  'active': true }
     * ];
     *
     * _.find(users, function(o) { return o.age < 40; });
     * // => object for 'barney'
     *
     * // The `_.matches` iteratee shorthand.
     * _.find(users, { 'age': 1, 'active': true });
     * // => object for 'pebbles'
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.find(users, ['active', false]);
     * // => object for 'fred'
     *
     * // The `_.property` iteratee shorthand.
     * _.find(users, 'active');
     * // => object for 'barney'
     */
    var find = createFind(findIndex);

    /**
     * This method is like `_.find` except that it iterates over elements of
     * `collection` from right to left.
     *
     * @static
     * @memberOf _
     * @since 2.0.0
     * @category Collection
     * @param {Array|Object} collection The collection to inspect.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @param {number} [fromIndex=collection.length-1] The index to search from.
     * @returns {*} Returns the matched element, else `undefined`.
     * @example
     *
     * _.findLast([1, 2, 3, 4], function(n) {
     *   return n % 2 == 1;
     * });
     * // => 3
     */
    var findLast = createFind(findLastIndex);

    /**
     * Creates a flattened array of values by running each element in `collection`
     * thru `iteratee` and flattening the mapped results. The iteratee is invoked
     * with three arguments: (value, index|key, collection).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @returns {Array} Returns the new flattened array.
     * @example
     *
     * function duplicate(n) {
     *   return [n, n];
     * }
     *
     * _.flatMap([1, 2], duplicate);
     * // => [1, 1, 2, 2]
     */
    function flatMap(collection, iteratee) {
      return baseFlatten(map(collection, iteratee), 1);
    }

    /**
     * This method is like `_.flatMap` except that it recursively flattens the
     * mapped results.
     *
     * @static
     * @memberOf _
     * @since 4.7.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @returns {Array} Returns the new flattened array.
     * @example
     *
     * function duplicate(n) {
     *   return [[[n, n]]];
     * }
     *
     * _.flatMapDeep([1, 2], duplicate);
     * // => [1, 1, 2, 2]
     */
    function flatMapDeep(collection, iteratee) {
      return baseFlatten(map(collection, iteratee), INFINITY);
    }

    /**
     * This method is like `_.flatMap` except that it recursively flattens the
     * mapped results up to `depth` times.
     *
     * @static
     * @memberOf _
     * @since 4.7.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @param {number} [depth=1] The maximum recursion depth.
     * @returns {Array} Returns the new flattened array.
     * @example
     *
     * function duplicate(n) {
     *   return [[[n, n]]];
     * }
     *
     * _.flatMapDepth([1, 2], duplicate, 2);
     * // => [[1, 1], [2, 2]]
     */
    function flatMapDepth(collection, iteratee, depth) {
      depth = depth === undefined ? 1 : toInteger(depth);
      return baseFlatten(map(collection, iteratee), depth);
    }

    /**
     * Iterates over elements of `collection` and invokes `iteratee` for each element.
     * The iteratee is invoked with three arguments: (value, index|key, collection).
     * Iteratee functions may exit iteration early by explicitly returning `false`.
     *
     * **Note:** As with other "Collections" methods, objects with a "length"
     * property are iterated like arrays. To avoid this behavior use `_.forIn`
     * or `_.forOwn` for object iteration.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @alias each
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @returns {Array|Object} Returns `collection`.
     * @see _.forEachRight
     * @example
     *
     * _.forEach([1, 2], function(value) {
     *   console.log(value);
     * });
     * // => Logs `1` then `2`.
     *
     * _.forEach({ 'a': 1, 'b': 2 }, function(value, key) {
     *   console.log(key);
     * });
     * // => Logs 'a' then 'b' (iteration order is not guaranteed).
     */
    function forEach(collection, iteratee) {
      var func = isArray(collection) ? arrayEach : baseEach;
      return func(collection, getIteratee(iteratee, 3));
    }

    /**
     * This method is like `_.forEach` except that it iterates over elements of
     * `collection` from right to left.
     *
     * @static
     * @memberOf _
     * @since 2.0.0
     * @alias eachRight
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @returns {Array|Object} Returns `collection`.
     * @see _.forEach
     * @example
     *
     * _.forEachRight([1, 2], function(value) {
     *   console.log(value);
     * });
     * // => Logs `2` then `1`.
     */
    function forEachRight(collection, iteratee) {
      var func = isArray(collection) ? arrayEachRight : baseEachRight;
      return func(collection, getIteratee(iteratee, 3));
    }

    /**
     * Creates an object composed of keys generated from the results of running
     * each element of `collection` thru `iteratee`. The order of grouped values
     * is determined by the order they occur in `collection`. The corresponding
     * value of each key is an array of elements responsible for generating the
     * key. The iteratee is invoked with one argument: (value).
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [iteratee=_.identity] The iteratee to transform keys.
     * @returns {Object} Returns the composed aggregate object.
     * @example
     *
     * _.groupBy([6.1, 4.2, 6.3], Math.floor);
     * // => { '4': [4.2], '6': [6.1, 6.3] }
     *
     * // The `_.property` iteratee shorthand.
     * _.groupBy(['one', 'two', 'three'], 'length');
     * // => { '3': ['one', 'two'], '5': ['three'] }
     */
    var groupBy = createAggregator(function(result, value, key) {
      if (hasOwnProperty.call(result, key)) {
        result[key].push(value);
      } else {
        baseAssignValue(result, key, [value]);
      }
    });

    /**
     * Checks if `value` is in `collection`. If `collection` is a string, it's
     * checked for a substring of `value`, otherwise
     * [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
     * is used for equality comparisons. If `fromIndex` is negative, it's used as
     * the offset from the end of `collection`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Collection
     * @param {Array|Object|string} collection The collection to inspect.
     * @param {*} value The value to search for.
     * @param {number} [fromIndex=0] The index to search from.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.reduce`.
     * @returns {boolean} Returns `true` if `value` is found, else `false`.
     * @example
     *
     * _.includes([1, 2, 3], 1);
     * // => true
     *
     * _.includes([1, 2, 3], 1, 2);
     * // => false
     *
     * _.includes({ 'a': 1, 'b': 2 }, 1);
     * // => true
     *
     * _.includes('abcd', 'bc');
     * // => true
     */
    function includes(collection, value, fromIndex, guard) {
      collection = isArrayLike(collection) ? collection : values(collection);
      fromIndex = (fromIndex && !guard) ? toInteger(fromIndex) : 0;

      var length = collection.length;
      if (fromIndex < 0) {
        fromIndex = nativeMax(length + fromIndex, 0);
      }
      return isString(collection)
        ? (fromIndex <= length && collection.indexOf(value, fromIndex) > -1)
        : (!!length && baseIndexOf(collection, value, fromIndex) > -1);
    }

    /**
     * Invokes the method at `path` of each element in `collection`, returning
     * an array of the results of each invoked method. Any additional arguments
     * are provided to each invoked method. If `path` is a function, it's invoked
     * for, and `this` bound to, each element in `collection`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Array|Function|string} path The path of the method to invoke or
     *  the function invoked per iteration.
     * @param {...*} [args] The arguments to invoke each method with.
     * @returns {Array} Returns the array of results.
     * @example
     *
     * _.invokeMap([[5, 1, 7], [3, 2, 1]], 'sort');
     * // => [[1, 5, 7], [1, 2, 3]]
     *
     * _.invokeMap([123, 456], String.prototype.split, '');
     * // => [['1', '2', '3'], ['4', '5', '6']]
     */
    var invokeMap = baseRest(function(collection, path, args) {
      var index = -1,
          isFunc = typeof path == 'function',
          result = isArrayLike(collection) ? Array(collection.length) : [];

      baseEach(collection, function(value) {
        result[++index] = isFunc ? apply(path, value, args) : baseInvoke(value, path, args);
      });
      return result;
    });

    /**
     * Creates an object composed of keys generated from the results of running
     * each element of `collection` thru `iteratee`. The corresponding value of
     * each key is the last element responsible for generating the key. The
     * iteratee is invoked with one argument: (value).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [iteratee=_.identity] The iteratee to transform keys.
     * @returns {Object} Returns the composed aggregate object.
     * @example
     *
     * var array = [
     *   { 'dir': 'left', 'code': 97 },
     *   { 'dir': 'right', 'code': 100 }
     * ];
     *
     * _.keyBy(array, function(o) {
     *   return String.fromCharCode(o.code);
     * });
     * // => { 'a': { 'dir': 'left', 'code': 97 }, 'd': { 'dir': 'right', 'code': 100 } }
     *
     * _.keyBy(array, 'dir');
     * // => { 'left': { 'dir': 'left', 'code': 97 }, 'right': { 'dir': 'right', 'code': 100 } }
     */
    var keyBy = createAggregator(function(result, value, key) {
      baseAssignValue(result, key, value);
    });

    /**
     * Creates an array of values by running each element in `collection` thru
     * `iteratee`. The iteratee is invoked with three arguments:
     * (value, index|key, collection).
     *
     * Many lodash methods are guarded to work as iteratees for methods like
     * `_.every`, `_.filter`, `_.map`, `_.mapValues`, `_.reject`, and `_.some`.
     *
     * The guarded methods are:
     * `ary`, `chunk`, `curry`, `curryRight`, `drop`, `dropRight`, `every`,
     * `fill`, `invert`, `parseInt`, `random`, `range`, `rangeRight`, `repeat`,
     * `sampleSize`, `slice`, `some`, `sortBy`, `split`, `take`, `takeRight`,
     * `template`, `trim`, `trimEnd`, `trimStart`, and `words`
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @returns {Array} Returns the new mapped array.
     * @example
     *
     * function square(n) {
     *   return n * n;
     * }
     *
     * _.map([4, 8], square);
     * // => [16, 64]
     *
     * _.map({ 'a': 4, 'b': 8 }, square);
     * // => [16, 64] (iteration order is not guaranteed)
     *
     * var users = [
     *   { 'user': 'barney' },
     *   { 'user': 'fred' }
     * ];
     *
     * // The `_.property` iteratee shorthand.
     * _.map(users, 'user');
     * // => ['barney', 'fred']
     */
    function map(collection, iteratee) {
      var func = isArray(collection) ? arrayMap : baseMap;
      return func(collection, getIteratee(iteratee, 3));
    }

    /**
     * This method is like `_.sortBy` except that it allows specifying the sort
     * orders of the iteratees to sort by. If `orders` is unspecified, all values
     * are sorted in ascending order. Otherwise, specify an order of "desc" for
     * descending or "asc" for ascending sort order of corresponding values.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Array[]|Function[]|Object[]|string[]} [iteratees=[_.identity]]
     *  The iteratees to sort by.
     * @param {string[]} [orders] The sort orders of `iteratees`.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.reduce`.
     * @returns {Array} Returns the new sorted array.
     * @example
     *
     * var users = [
     *   { 'user': 'fred',   'age': 48 },
     *   { 'user': 'barney', 'age': 34 },
     *   { 'user': 'fred',   'age': 40 },
     *   { 'user': 'barney', 'age': 36 }
     * ];
     *
     * // Sort by `user` in ascending order and by `age` in descending order.
     * _.orderBy(users, ['user', 'age'], ['asc', 'desc']);
     * // => objects for [['barney', 36], ['barney', 34], ['fred', 48], ['fred', 40]]
     */
    function orderBy(collection, iteratees, orders, guard) {
      if (collection == null) {
        return [];
      }
      if (!isArray(iteratees)) {
        iteratees = iteratees == null ? [] : [iteratees];
      }
      orders = guard ? undefined : orders;
      if (!isArray(orders)) {
        orders = orders == null ? [] : [orders];
      }
      return baseOrderBy(collection, iteratees, orders);
    }

    /**
     * Creates an array of elements split into two groups, the first of which
     * contains elements `predicate` returns truthy for, the second of which
     * contains elements `predicate` returns falsey for. The predicate is
     * invoked with one argument: (value).
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @returns {Array} Returns the array of grouped elements.
     * @example
     *
     * var users = [
     *   { 'user': 'barney',  'age': 36, 'active': false },
     *   { 'user': 'fred',    'age': 40, 'active': true },
     *   { 'user': 'pebbles', 'age': 1,  'active': false }
     * ];
     *
     * _.partition(users, function(o) { return o.active; });
     * // => objects for [['fred'], ['barney', 'pebbles']]
     *
     * // The `_.matches` iteratee shorthand.
     * _.partition(users, { 'age': 1, 'active': false });
     * // => objects for [['pebbles'], ['barney', 'fred']]
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.partition(users, ['active', false]);
     * // => objects for [['barney', 'pebbles'], ['fred']]
     *
     * // The `_.property` iteratee shorthand.
     * _.partition(users, 'active');
     * // => objects for [['fred'], ['barney', 'pebbles']]
     */
    var partition = createAggregator(function(result, value, key) {
      result[key ? 0 : 1].push(value);
    }, function() { return [[], []]; });

    /**
     * Reduces `collection` to a value which is the accumulated result of running
     * each element in `collection` thru `iteratee`, where each successive
     * invocation is supplied the return value of the previous. If `accumulator`
     * is not given, the first element of `collection` is used as the initial
     * value. The iteratee is invoked with four arguments:
     * (accumulator, value, index|key, collection).
     *
     * Many lodash methods are guarded to work as iteratees for methods like
     * `_.reduce`, `_.reduceRight`, and `_.transform`.
     *
     * The guarded methods are:
     * `assign`, `defaults`, `defaultsDeep`, `includes`, `merge`, `orderBy`,
     * and `sortBy`
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @param {*} [accumulator] The initial value.
     * @returns {*} Returns the accumulated value.
     * @see _.reduceRight
     * @example
     *
     * _.reduce([1, 2], function(sum, n) {
     *   return sum + n;
     * }, 0);
     * // => 3
     *
     * _.reduce({ 'a': 1, 'b': 2, 'c': 1 }, function(result, value, key) {
     *   (result[value] || (result[value] = [])).push(key);
     *   return result;
     * }, {});
     * // => { '1': ['a', 'c'], '2': ['b'] } (iteration order is not guaranteed)
     */
    function reduce(collection, iteratee, accumulator) {
      var func = isArray(collection) ? arrayReduce : baseReduce,
          initAccum = arguments.length < 3;

      return func(collection, getIteratee(iteratee, 4), accumulator, initAccum, baseEach);
    }

    /**
     * This method is like `_.reduce` except that it iterates over elements of
     * `collection` from right to left.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @param {*} [accumulator] The initial value.
     * @returns {*} Returns the accumulated value.
     * @see _.reduce
     * @example
     *
     * var array = [[0, 1], [2, 3], [4, 5]];
     *
     * _.reduceRight(array, function(flattened, other) {
     *   return flattened.concat(other);
     * }, []);
     * // => [4, 5, 2, 3, 0, 1]
     */
    function reduceRight(collection, iteratee, accumulator) {
      var func = isArray(collection) ? arrayReduceRight : baseReduce,
          initAccum = arguments.length < 3;

      return func(collection, getIteratee(iteratee, 4), accumulator, initAccum, baseEachRight);
    }

    /**
     * The opposite of `_.filter`; this method returns the elements of `collection`
     * that `predicate` does **not** return truthy for.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @returns {Array} Returns the new filtered array.
     * @see _.filter
     * @example
     *
     * var users = [
     *   { 'user': 'barney', 'age': 36, 'active': false },
     *   { 'user': 'fred',   'age': 40, 'active': true }
     * ];
     *
     * _.reject(users, function(o) { return !o.active; });
     * // => objects for ['fred']
     *
     * // The `_.matches` iteratee shorthand.
     * _.reject(users, { 'age': 40, 'active': true });
     * // => objects for ['barney']
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.reject(users, ['active', false]);
     * // => objects for ['fred']
     *
     * // The `_.property` iteratee shorthand.
     * _.reject(users, 'active');
     * // => objects for ['barney']
     */
    function reject(collection, predicate) {
      var func = isArray(collection) ? arrayFilter : baseFilter;
      return func(collection, negate(getIteratee(predicate, 3)));
    }

    /**
     * Gets a random element from `collection`.
     *
     * @static
     * @memberOf _
     * @since 2.0.0
     * @category Collection
     * @param {Array|Object} collection The collection to sample.
     * @returns {*} Returns the random element.
     * @example
     *
     * _.sample([1, 2, 3, 4]);
     * // => 2
     */
    function sample(collection) {
      var func = isArray(collection) ? arraySample : baseSample;
      return func(collection);
    }

    /**
     * Gets `n` random elements at unique keys from `collection` up to the
     * size of `collection`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Collection
     * @param {Array|Object} collection The collection to sample.
     * @param {number} [n=1] The number of elements to sample.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {Array} Returns the random elements.
     * @example
     *
     * _.sampleSize([1, 2, 3], 2);
     * // => [3, 1]
     *
     * _.sampleSize([1, 2, 3], 4);
     * // => [2, 3, 1]
     */
    function sampleSize(collection, n, guard) {
      if ((guard ? isIterateeCall(collection, n, guard) : n === undefined)) {
        n = 1;
      } else {
        n = toInteger(n);
      }
      var func = isArray(collection) ? arraySampleSize : baseSampleSize;
      return func(collection, n);
    }

    /**
     * Creates an array of shuffled values, using a version of the
     * [Fisher-Yates shuffle](https://en.wikipedia.org/wiki/Fisher-Yates_shuffle).
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Collection
     * @param {Array|Object} collection The collection to shuffle.
     * @returns {Array} Returns the new shuffled array.
     * @example
     *
     * _.shuffle([1, 2, 3, 4]);
     * // => [4, 1, 3, 2]
     */
    function shuffle(collection) {
      var func = isArray(collection) ? arrayShuffle : baseShuffle;
      return func(collection);
    }

    /**
     * Gets the size of `collection` by returning its length for array-like
     * values or the number of own enumerable string keyed properties for objects.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Collection
     * @param {Array|Object|string} collection The collection to inspect.
     * @returns {number} Returns the collection size.
     * @example
     *
     * _.size([1, 2, 3]);
     * // => 3
     *
     * _.size({ 'a': 1, 'b': 2 });
     * // => 2
     *
     * _.size('pebbles');
     * // => 7
     */
    function size(collection) {
      if (collection == null) {
        return 0;
      }
      if (isArrayLike(collection)) {
        return isString(collection) ? stringSize(collection) : collection.length;
      }
      var tag = getTag(collection);
      if (tag == mapTag || tag == setTag) {
        return collection.size;
      }
      return baseKeys(collection).length;
    }

    /**
     * Checks if `predicate` returns truthy for **any** element of `collection`.
     * Iteration is stopped once `predicate` returns truthy. The predicate is
     * invoked with three arguments: (value, index|key, collection).
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {boolean} Returns `true` if any element passes the predicate check,
     *  else `false`.
     * @example
     *
     * _.some([null, 0, 'yes', false], Boolean);
     * // => true
     *
     * var users = [
     *   { 'user': 'barney', 'active': true },
     *   { 'user': 'fred',   'active': false }
     * ];
     *
     * // The `_.matches` iteratee shorthand.
     * _.some(users, { 'user': 'barney', 'active': false });
     * // => false
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.some(users, ['active', false]);
     * // => true
     *
     * // The `_.property` iteratee shorthand.
     * _.some(users, 'active');
     * // => true
     */
    function some(collection, predicate, guard) {
      var func = isArray(collection) ? arraySome : baseSome;
      if (guard && isIterateeCall(collection, predicate, guard)) {
        predicate = undefined;
      }
      return func(collection, getIteratee(predicate, 3));
    }

    /**
     * Creates an array of elements, sorted in ascending order by the results of
     * running each element in a collection thru each iteratee. This method
     * performs a stable sort, that is, it preserves the original sort order of
     * equal elements. The iteratees are invoked with one argument: (value).
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Collection
     * @param {Array|Object} collection The collection to iterate over.
     * @param {...(Function|Function[])} [iteratees=[_.identity]]
     *  The iteratees to sort by.
     * @returns {Array} Returns the new sorted array.
     * @example
     *
     * var users = [
     *   { 'user': 'fred',   'age': 48 },
     *   { 'user': 'barney', 'age': 36 },
     *   { 'user': 'fred',   'age': 30 },
     *   { 'user': 'barney', 'age': 34 }
     * ];
     *
     * _.sortBy(users, [function(o) { return o.user; }]);
     * // => objects for [['barney', 36], ['barney', 34], ['fred', 48], ['fred', 30]]
     *
     * _.sortBy(users, ['user', 'age']);
     * // => objects for [['barney', 34], ['barney', 36], ['fred', 30], ['fred', 48]]
     */
    var sortBy = baseRest(function(collection, iteratees) {
      if (collection == null) {
        return [];
      }
      var length = iteratees.length;
      if (length > 1 && isIterateeCall(collection, iteratees[0], iteratees[1])) {
        iteratees = [];
      } else if (length > 2 && isIterateeCall(iteratees[0], iteratees[1], iteratees[2])) {
        iteratees = [iteratees[0]];
      }
      return baseOrderBy(collection, baseFlatten(iteratees, 1), []);
    });

    /*------------------------------------------------------------------------*/

    /**
     * Gets the timestamp of the number of milliseconds that have elapsed since
     * the Unix epoch (1 January 1970 00:00:00 UTC).
     *
     * @static
     * @memberOf _
     * @since 2.4.0
     * @category Date
     * @returns {number} Returns the timestamp.
     * @example
     *
     * _.defer(function(stamp) {
     *   console.log(_.now() - stamp);
     * }, _.now());
     * // => Logs the number of milliseconds it took for the deferred invocation.
     */
    var now = ctxNow || function() {
      return root.Date.now();
    };

    /*------------------------------------------------------------------------*/

    /**
     * The opposite of `_.before`; this method creates a function that invokes
     * `func` once it's called `n` or more times.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Function
     * @param {number} n The number of calls before `func` is invoked.
     * @param {Function} func The function to restrict.
     * @returns {Function} Returns the new restricted function.
     * @example
     *
     * var saves = ['profile', 'settings'];
     *
     * var done = _.after(saves.length, function() {
     *   console.log('done saving!');
     * });
     *
     * _.forEach(saves, function(type) {
     *   asyncSave({ 'type': type, 'complete': done });
     * });
     * // => Logs 'done saving!' after the two async saves have completed.
     */
    function after(n, func) {
      if (typeof func != 'function') {
        throw new TypeError(FUNC_ERROR_TEXT);
      }
      n = toInteger(n);
      return function() {
        if (--n < 1) {
          return func.apply(this, arguments);
        }
      };
    }

    /**
     * Creates a function that invokes `func`, with up to `n` arguments,
     * ignoring any additional arguments.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Function
     * @param {Function} func The function to cap arguments for.
     * @param {number} [n=func.length] The arity cap.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {Function} Returns the new capped function.
     * @example
     *
     * _.map(['6', '8', '10'], _.ary(parseInt, 1));
     * // => [6, 8, 10]
     */
    function ary(func, n, guard) {
      n = guard ? undefined : n;
      n = (func && n == null) ? func.length : n;
      return createWrap(func, WRAP_ARY_FLAG, undefined, undefined, undefined, undefined, n);
    }

    /**
     * Creates a function that invokes `func`, with the `this` binding and arguments
     * of the created function, while it's called less than `n` times. Subsequent
     * calls to the created function return the result of the last `func` invocation.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Function
     * @param {number} n The number of calls at which `func` is no longer invoked.
     * @param {Function} func The function to restrict.
     * @returns {Function} Returns the new restricted function.
     * @example
     *
     * jQuery(element).on('click', _.before(5, addContactToList));
     * // => Allows adding up to 4 contacts to the list.
     */
    function before(n, func) {
      var result;
      if (typeof func != 'function') {
        throw new TypeError(FUNC_ERROR_TEXT);
      }
      n = toInteger(n);
      return function() {
        if (--n > 0) {
          result = func.apply(this, arguments);
        }
        if (n <= 1) {
          func = undefined;
        }
        return result;
      };
    }

    /**
     * Creates a function that invokes `func` with the `this` binding of `thisArg`
     * and `partials` prepended to the arguments it receives.
     *
     * The `_.bind.placeholder` value, which defaults to `_` in monolithic builds,
     * may be used as a placeholder for partially applied arguments.
     *
     * **Note:** Unlike native `Function#bind`, this method doesn't set the "length"
     * property of bound functions.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Function
     * @param {Function} func The function to bind.
     * @param {*} thisArg The `this` binding of `func`.
     * @param {...*} [partials] The arguments to be partially applied.
     * @returns {Function} Returns the new bound function.
     * @example
     *
     * function greet(greeting, punctuation) {
     *   return greeting + ' ' + this.user + punctuation;
     * }
     *
     * var object = { 'user': 'fred' };
     *
     * var bound = _.bind(greet, object, 'hi');
     * bound('!');
     * // => 'hi fred!'
     *
     * // Bound with placeholders.
     * var bound = _.bind(greet, object, _, '!');
     * bound('hi');
     * // => 'hi fred!'
     */
    var bind = baseRest(function(func, thisArg, partials) {
      var bitmask = WRAP_BIND_FLAG;
      if (partials.length) {
        var holders = replaceHolders(partials, getHolder(bind));
        bitmask |= WRAP_PARTIAL_FLAG;
      }
      return createWrap(func, bitmask, thisArg, partials, holders);
    });

    /**
     * Creates a function that invokes the method at `object[key]` with `partials`
     * prepended to the arguments it receives.
     *
     * This method differs from `_.bind` by allowing bound functions to reference
     * methods that may be redefined or don't yet exist. See
     * [Peter Michaux's article](http://peter.michaux.ca/articles/lazy-function-definition-pattern)
     * for more details.
     *
     * The `_.bindKey.placeholder` value, which defaults to `_` in monolithic
     * builds, may be used as a placeholder for partially applied arguments.
     *
     * @static
     * @memberOf _
     * @since 0.10.0
     * @category Function
     * @param {Object} object The object to invoke the method on.
     * @param {string} key The key of the method.
     * @param {...*} [partials] The arguments to be partially applied.
     * @returns {Function} Returns the new bound function.
     * @example
     *
     * var object = {
     *   'user': 'fred',
     *   'greet': function(greeting, punctuation) {
     *     return greeting + ' ' + this.user + punctuation;
     *   }
     * };
     *
     * var bound = _.bindKey(object, 'greet', 'hi');
     * bound('!');
     * // => 'hi fred!'
     *
     * object.greet = function(greeting, punctuation) {
     *   return greeting + 'ya ' + this.user + punctuation;
     * };
     *
     * bound('!');
     * // => 'hiya fred!'
     *
     * // Bound with placeholders.
     * var bound = _.bindKey(object, 'greet', _, '!');
     * bound('hi');
     * // => 'hiya fred!'
     */
    var bindKey = baseRest(function(object, key, partials) {
      var bitmask = WRAP_BIND_FLAG | WRAP_BIND_KEY_FLAG;
      if (partials.length) {
        var holders = replaceHolders(partials, getHolder(bindKey));
        bitmask |= WRAP_PARTIAL_FLAG;
      }
      return createWrap(key, bitmask, object, partials, holders);
    });

    /**
     * Creates a function that accepts arguments of `func` and either invokes
     * `func` returning its result, if at least `arity` number of arguments have
     * been provided, or returns a function that accepts the remaining `func`
     * arguments, and so on. The arity of `func` may be specified if `func.length`
     * is not sufficient.
     *
     * The `_.curry.placeholder` value, which defaults to `_` in monolithic builds,
     * may be used as a placeholder for provided arguments.
     *
     * **Note:** This method doesn't set the "length" property of curried functions.
     *
     * @static
     * @memberOf _
     * @since 2.0.0
     * @category Function
     * @param {Function} func The function to curry.
     * @param {number} [arity=func.length] The arity of `func`.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {Function} Returns the new curried function.
     * @example
     *
     * var abc = function(a, b, c) {
     *   return [a, b, c];
     * };
     *
     * var curried = _.curry(abc);
     *
     * curried(1)(2)(3);
     * // => [1, 2, 3]
     *
     * curried(1, 2)(3);
     * // => [1, 2, 3]
     *
     * curried(1, 2, 3);
     * // => [1, 2, 3]
     *
     * // Curried with placeholders.
     * curried(1)(_, 3)(2);
     * // => [1, 2, 3]
     */
    function curry(func, arity, guard) {
      arity = guard ? undefined : arity;
      var result = createWrap(func, WRAP_CURRY_FLAG, undefined, undefined, undefined, undefined, undefined, arity);
      result.placeholder = curry.placeholder;
      return result;
    }

    /**
     * This method is like `_.curry` except that arguments are applied to `func`
     * in the manner of `_.partialRight` instead of `_.partial`.
     *
     * The `_.curryRight.placeholder` value, which defaults to `_` in monolithic
     * builds, may be used as a placeholder for provided arguments.
     *
     * **Note:** This method doesn't set the "length" property of curried functions.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Function
     * @param {Function} func The function to curry.
     * @param {number} [arity=func.length] The arity of `func`.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {Function} Returns the new curried function.
     * @example
     *
     * var abc = function(a, b, c) {
     *   return [a, b, c];
     * };
     *
     * var curried = _.curryRight(abc);
     *
     * curried(3)(2)(1);
     * // => [1, 2, 3]
     *
     * curried(2, 3)(1);
     * // => [1, 2, 3]
     *
     * curried(1, 2, 3);
     * // => [1, 2, 3]
     *
     * // Curried with placeholders.
     * curried(3)(1, _)(2);
     * // => [1, 2, 3]
     */
    function curryRight(func, arity, guard) {
      arity = guard ? undefined : arity;
      var result = createWrap(func, WRAP_CURRY_RIGHT_FLAG, undefined, undefined, undefined, undefined, undefined, arity);
      result.placeholder = curryRight.placeholder;
      return result;
    }

    /**
     * Creates a debounced function that delays invoking `func` until after `wait`
     * milliseconds have elapsed since the last time the debounced function was
     * invoked. The debounced function comes with a `cancel` method to cancel
     * delayed `func` invocations and a `flush` method to immediately invoke them.
     * Provide `options` to indicate whether `func` should be invoked on the
     * leading and/or trailing edge of the `wait` timeout. The `func` is invoked
     * with the last arguments provided to the debounced function. Subsequent
     * calls to the debounced function return the result of the last `func`
     * invocation.
     *
     * **Note:** If `leading` and `trailing` options are `true`, `func` is
     * invoked on the trailing edge of the timeout only if the debounced function
     * is invoked more than once during the `wait` timeout.
     *
     * If `wait` is `0` and `leading` is `false`, `func` invocation is deferred
     * until to the next tick, similar to `setTimeout` with a timeout of `0`.
     *
     * See [David Corbacho's article](https://css-tricks.com/debouncing-throttling-explained-examples/)
     * for details over the differences between `_.debounce` and `_.throttle`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Function
     * @param {Function} func The function to debounce.
     * @param {number} [wait=0] The number of milliseconds to delay.
     * @param {Object} [options={}] The options object.
     * @param {boolean} [options.leading=false]
     *  Specify invoking on the leading edge of the timeout.
     * @param {number} [options.maxWait]
     *  The maximum time `func` is allowed to be delayed before it's invoked.
     * @param {boolean} [options.trailing=true]
     *  Specify invoking on the trailing edge of the timeout.
     * @returns {Function} Returns the new debounced function.
     * @example
     *
     * // Avoid costly calculations while the window size is in flux.
     * jQuery(window).on('resize', _.debounce(calculateLayout, 150));
     *
     * // Invoke `sendMail` when clicked, debouncing subsequent calls.
     * jQuery(element).on('click', _.debounce(sendMail, 300, {
     *   'leading': true,
     *   'trailing': false
     * }));
     *
     * // Ensure `batchLog` is invoked once after 1 second of debounced calls.
     * var debounced = _.debounce(batchLog, 250, { 'maxWait': 1000 });
     * var source = new EventSource('/stream');
     * jQuery(source).on('message', debounced);
     *
     * // Cancel the trailing debounced invocation.
     * jQuery(window).on('popstate', debounced.cancel);
     */
    function debounce(func, wait, options) {
      var lastArgs,
          lastThis,
          maxWait,
          result,
          timerId,
          lastCallTime,
          lastInvokeTime = 0,
          leading = false,
          maxing = false,
          trailing = true;

      if (typeof func != 'function') {
        throw new TypeError(FUNC_ERROR_TEXT);
      }
      wait = toNumber(wait) || 0;
      if (isObject(options)) {
        leading = !!options.leading;
        maxing = 'maxWait' in options;
        maxWait = maxing ? nativeMax(toNumber(options.maxWait) || 0, wait) : maxWait;
        trailing = 'trailing' in options ? !!options.trailing : trailing;
      }

      function invokeFunc(time) {
        var args = lastArgs,
            thisArg = lastThis;

        lastArgs = lastThis = undefined;
        lastInvokeTime = time;
        result = func.apply(thisArg, args);
        return result;
      }

      function leadingEdge(time) {
        // Reset any `maxWait` timer.
        lastInvokeTime = time;
        // Start the timer for the trailing edge.
        timerId = setTimeout(timerExpired, wait);
        // Invoke the leading edge.
        return leading ? invokeFunc(time) : result;
      }

      function remainingWait(time) {
        var timeSinceLastCall = time - lastCallTime,
            timeSinceLastInvoke = time - lastInvokeTime,
            timeWaiting = wait - timeSinceLastCall;

        return maxing
          ? nativeMin(timeWaiting, maxWait - timeSinceLastInvoke)
          : timeWaiting;
      }

      function shouldInvoke(time) {
        var timeSinceLastCall = time - lastCallTime,
            timeSinceLastInvoke = time - lastInvokeTime;

        // Either this is the first call, activity has stopped and we're at the
        // trailing edge, the system time has gone backwards and we're treating
        // it as the trailing edge, or we've hit the `maxWait` limit.
        return (lastCallTime === undefined || (timeSinceLastCall >= wait) ||
          (timeSinceLastCall < 0) || (maxing && timeSinceLastInvoke >= maxWait));
      }

      function timerExpired() {
        var time = now();
        if (shouldInvoke(time)) {
          return trailingEdge(time);
        }
        // Restart the timer.
        timerId = setTimeout(timerExpired, remainingWait(time));
      }

      function trailingEdge(time) {
        timerId = undefined;

        // Only invoke if we have `lastArgs` which means `func` has been
        // debounced at least once.
        if (trailing && lastArgs) {
          return invokeFunc(time);
        }
        lastArgs = lastThis = undefined;
        return result;
      }

      function cancel() {
        if (timerId !== undefined) {
          clearTimeout(timerId);
        }
        lastInvokeTime = 0;
        lastArgs = lastCallTime = lastThis = timerId = undefined;
      }

      function flush() {
        return timerId === undefined ? result : trailingEdge(now());
      }

      function debounced() {
        var time = now(),
            isInvoking = shouldInvoke(time);

        lastArgs = arguments;
        lastThis = this;
        lastCallTime = time;

        if (isInvoking) {
          if (timerId === undefined) {
            return leadingEdge(lastCallTime);
          }
          if (maxing) {
            // Handle invocations in a tight loop.
            clearTimeout(timerId);
            timerId = setTimeout(timerExpired, wait);
            return invokeFunc(lastCallTime);
          }
        }
        if (timerId === undefined) {
          timerId = setTimeout(timerExpired, wait);
        }
        return result;
      }
      debounced.cancel = cancel;
      debounced.flush = flush;
      return debounced;
    }

    /**
     * Defers invoking the `func` until the current call stack has cleared. Any
     * additional arguments are provided to `func` when it's invoked.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Function
     * @param {Function} func The function to defer.
     * @param {...*} [args] The arguments to invoke `func` with.
     * @returns {number} Returns the timer id.
     * @example
     *
     * _.defer(function(text) {
     *   console.log(text);
     * }, 'deferred');
     * // => Logs 'deferred' after one millisecond.
     */
    var defer = baseRest(function(func, args) {
      return baseDelay(func, 1, args);
    });

    /**
     * Invokes `func` after `wait` milliseconds. Any additional arguments are
     * provided to `func` when it's invoked.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Function
     * @param {Function} func The function to delay.
     * @param {number} wait The number of milliseconds to delay invocation.
     * @param {...*} [args] The arguments to invoke `func` with.
     * @returns {number} Returns the timer id.
     * @example
     *
     * _.delay(function(text) {
     *   console.log(text);
     * }, 1000, 'later');
     * // => Logs 'later' after one second.
     */
    var delay = baseRest(function(func, wait, args) {
      return baseDelay(func, toNumber(wait) || 0, args);
    });

    /**
     * Creates a function that invokes `func` with arguments reversed.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Function
     * @param {Function} func The function to flip arguments for.
     * @returns {Function} Returns the new flipped function.
     * @example
     *
     * var flipped = _.flip(function() {
     *   return _.toArray(arguments);
     * });
     *
     * flipped('a', 'b', 'c', 'd');
     * // => ['d', 'c', 'b', 'a']
     */
    function flip(func) {
      return createWrap(func, WRAP_FLIP_FLAG);
    }

    /**
     * Creates a function that memoizes the result of `func`. If `resolver` is
     * provided, it determines the cache key for storing the result based on the
     * arguments provided to the memoized function. By default, the first argument
     * provided to the memoized function is used as the map cache key. The `func`
     * is invoked with the `this` binding of the memoized function.
     *
     * **Note:** The cache is exposed as the `cache` property on the memoized
     * function. Its creation may be customized by replacing the `_.memoize.Cache`
     * constructor with one whose instances implement the
     * [`Map`](http://ecma-international.org/ecma-262/7.0/#sec-properties-of-the-map-prototype-object)
     * method interface of `clear`, `delete`, `get`, `has`, and `set`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Function
     * @param {Function} func The function to have its output memoized.
     * @param {Function} [resolver] The function to resolve the cache key.
     * @returns {Function} Returns the new memoized function.
     * @example
     *
     * var object = { 'a': 1, 'b': 2 };
     * var other = { 'c': 3, 'd': 4 };
     *
     * var values = _.memoize(_.values);
     * values(object);
     * // => [1, 2]
     *
     * values(other);
     * // => [3, 4]
     *
     * object.a = 2;
     * values(object);
     * // => [1, 2]
     *
     * // Modify the result cache.
     * values.cache.set(object, ['a', 'b']);
     * values(object);
     * // => ['a', 'b']
     *
     * // Replace `_.memoize.Cache`.
     * _.memoize.Cache = WeakMap;
     */
    function memoize(func, resolver) {
      if (typeof func != 'function' || (resolver != null && typeof resolver != 'function')) {
        throw new TypeError(FUNC_ERROR_TEXT);
      }
      var memoized = function() {
        var args = arguments,
            key = resolver ? resolver.apply(this, args) : args[0],
            cache = memoized.cache;

        if (cache.has(key)) {
          return cache.get(key);
        }
        var result = func.apply(this, args);
        memoized.cache = cache.set(key, result) || cache;
        return result;
      };
      memoized.cache = new (memoize.Cache || MapCache);
      return memoized;
    }

    // Expose `MapCache`.
    memoize.Cache = MapCache;

    /**
     * Creates a function that negates the result of the predicate `func`. The
     * `func` predicate is invoked with the `this` binding and arguments of the
     * created function.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Function
     * @param {Function} predicate The predicate to negate.
     * @returns {Function} Returns the new negated function.
     * @example
     *
     * function isEven(n) {
     *   return n % 2 == 0;
     * }
     *
     * _.filter([1, 2, 3, 4, 5, 6], _.negate(isEven));
     * // => [1, 3, 5]
     */
    function negate(predicate) {
      if (typeof predicate != 'function') {
        throw new TypeError(FUNC_ERROR_TEXT);
      }
      return function() {
        var args = arguments;
        switch (args.length) {
          case 0: return !predicate.call(this);
          case 1: return !predicate.call(this, args[0]);
          case 2: return !predicate.call(this, args[0], args[1]);
          case 3: return !predicate.call(this, args[0], args[1], args[2]);
        }
        return !predicate.apply(this, args);
      };
    }

    /**
     * Creates a function that is restricted to invoking `func` once. Repeat calls
     * to the function return the value of the first invocation. The `func` is
     * invoked with the `this` binding and arguments of the created function.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Function
     * @param {Function} func The function to restrict.
     * @returns {Function} Returns the new restricted function.
     * @example
     *
     * var initialize = _.once(createApplication);
     * initialize();
     * initialize();
     * // => `createApplication` is invoked once
     */
    function once(func) {
      return before(2, func);
    }

    /**
     * Creates a function that invokes `func` with its arguments transformed.
     *
     * @static
     * @since 4.0.0
     * @memberOf _
     * @category Function
     * @param {Function} func The function to wrap.
     * @param {...(Function|Function[])} [transforms=[_.identity]]
     *  The argument transforms.
     * @returns {Function} Returns the new function.
     * @example
     *
     * function doubled(n) {
     *   return n * 2;
     * }
     *
     * function square(n) {
     *   return n * n;
     * }
     *
     * var func = _.overArgs(function(x, y) {
     *   return [x, y];
     * }, [square, doubled]);
     *
     * func(9, 3);
     * // => [81, 6]
     *
     * func(10, 5);
     * // => [100, 10]
     */
    var overArgs = castRest(function(func, transforms) {
      transforms = (transforms.length == 1 && isArray(transforms[0]))
        ? arrayMap(transforms[0], baseUnary(getIteratee()))
        : arrayMap(baseFlatten(transforms, 1), baseUnary(getIteratee()));

      var funcsLength = transforms.length;
      return baseRest(function(args) {
        var index = -1,
            length = nativeMin(args.length, funcsLength);

        while (++index < length) {
          args[index] = transforms[index].call(this, args[index]);
        }
        return apply(func, this, args);
      });
    });

    /**
     * Creates a function that invokes `func` with `partials` prepended to the
     * arguments it receives. This method is like `_.bind` except it does **not**
     * alter the `this` binding.
     *
     * The `_.partial.placeholder` value, which defaults to `_` in monolithic
     * builds, may be used as a placeholder for partially applied arguments.
     *
     * **Note:** This method doesn't set the "length" property of partially
     * applied functions.
     *
     * @static
     * @memberOf _
     * @since 0.2.0
     * @category Function
     * @param {Function} func The function to partially apply arguments to.
     * @param {...*} [partials] The arguments to be partially applied.
     * @returns {Function} Returns the new partially applied function.
     * @example
     *
     * function greet(greeting, name) {
     *   return greeting + ' ' + name;
     * }
     *
     * var sayHelloTo = _.partial(greet, 'hello');
     * sayHelloTo('fred');
     * // => 'hello fred'
     *
     * // Partially applied with placeholders.
     * var greetFred = _.partial(greet, _, 'fred');
     * greetFred('hi');
     * // => 'hi fred'
     */
    var partial = baseRest(function(func, partials) {
      var holders = replaceHolders(partials, getHolder(partial));
      return createWrap(func, WRAP_PARTIAL_FLAG, undefined, partials, holders);
    });

    /**
     * This method is like `_.partial` except that partially applied arguments
     * are appended to the arguments it receives.
     *
     * The `_.partialRight.placeholder` value, which defaults to `_` in monolithic
     * builds, may be used as a placeholder for partially applied arguments.
     *
     * **Note:** This method doesn't set the "length" property of partially
     * applied functions.
     *
     * @static
     * @memberOf _
     * @since 1.0.0
     * @category Function
     * @param {Function} func The function to partially apply arguments to.
     * @param {...*} [partials] The arguments to be partially applied.
     * @returns {Function} Returns the new partially applied function.
     * @example
     *
     * function greet(greeting, name) {
     *   return greeting + ' ' + name;
     * }
     *
     * var greetFred = _.partialRight(greet, 'fred');
     * greetFred('hi');
     * // => 'hi fred'
     *
     * // Partially applied with placeholders.
     * var sayHelloTo = _.partialRight(greet, 'hello', _);
     * sayHelloTo('fred');
     * // => 'hello fred'
     */
    var partialRight = baseRest(function(func, partials) {
      var holders = replaceHolders(partials, getHolder(partialRight));
      return createWrap(func, WRAP_PARTIAL_RIGHT_FLAG, undefined, partials, holders);
    });

    /**
     * Creates a function that invokes `func` with arguments arranged according
     * to the specified `indexes` where the argument value at the first index is
     * provided as the first argument, the argument value at the second index is
     * provided as the second argument, and so on.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Function
     * @param {Function} func The function to rearrange arguments for.
     * @param {...(number|number[])} indexes The arranged argument indexes.
     * @returns {Function} Returns the new function.
     * @example
     *
     * var rearged = _.rearg(function(a, b, c) {
     *   return [a, b, c];
     * }, [2, 0, 1]);
     *
     * rearged('b', 'c', 'a')
     * // => ['a', 'b', 'c']
     */
    var rearg = flatRest(function(func, indexes) {
      return createWrap(func, WRAP_REARG_FLAG, undefined, undefined, undefined, indexes);
    });

    /**
     * Creates a function that invokes `func` with the `this` binding of the
     * created function and arguments from `start` and beyond provided as
     * an array.
     *
     * **Note:** This method is based on the
     * [rest parameter](https://mdn.io/rest_parameters).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Function
     * @param {Function} func The function to apply a rest parameter to.
     * @param {number} [start=func.length-1] The start position of the rest parameter.
     * @returns {Function} Returns the new function.
     * @example
     *
     * var say = _.rest(function(what, names) {
     *   return what + ' ' + _.initial(names).join(', ') +
     *     (_.size(names) > 1 ? ', & ' : '') + _.last(names);
     * });
     *
     * say('hello', 'fred', 'barney', 'pebbles');
     * // => 'hello fred, barney, & pebbles'
     */
    function rest(func, start) {
      if (typeof func != 'function') {
        throw new TypeError(FUNC_ERROR_TEXT);
      }
      start = start === undefined ? start : toInteger(start);
      return baseRest(func, start);
    }

    /**
     * Creates a function that invokes `func` with the `this` binding of the
     * create function and an array of arguments much like
     * [`Function#apply`](http://www.ecma-international.org/ecma-262/7.0/#sec-function.prototype.apply).
     *
     * **Note:** This method is based on the
     * [spread operator](https://mdn.io/spread_operator).
     *
     * @static
     * @memberOf _
     * @since 3.2.0
     * @category Function
     * @param {Function} func The function to spread arguments over.
     * @param {number} [start=0] The start position of the spread.
     * @returns {Function} Returns the new function.
     * @example
     *
     * var say = _.spread(function(who, what) {
     *   return who + ' says ' + what;
     * });
     *
     * say(['fred', 'hello']);
     * // => 'fred says hello'
     *
     * var numbers = Promise.all([
     *   Promise.resolve(40),
     *   Promise.resolve(36)
     * ]);
     *
     * numbers.then(_.spread(function(x, y) {
     *   return x + y;
     * }));
     * // => a Promise of 76
     */
    function spread(func, start) {
      if (typeof func != 'function') {
        throw new TypeError(FUNC_ERROR_TEXT);
      }
      start = start == null ? 0 : nativeMax(toInteger(start), 0);
      return baseRest(function(args) {
        var array = args[start],
            otherArgs = castSlice(args, 0, start);

        if (array) {
          arrayPush(otherArgs, array);
        }
        return apply(func, this, otherArgs);
      });
    }

    /**
     * Creates a throttled function that only invokes `func` at most once per
     * every `wait` milliseconds. The throttled function comes with a `cancel`
     * method to cancel delayed `func` invocations and a `flush` method to
     * immediately invoke them. Provide `options` to indicate whether `func`
     * should be invoked on the leading and/or trailing edge of the `wait`
     * timeout. The `func` is invoked with the last arguments provided to the
     * throttled function. Subsequent calls to the throttled function return the
     * result of the last `func` invocation.
     *
     * **Note:** If `leading` and `trailing` options are `true`, `func` is
     * invoked on the trailing edge of the timeout only if the throttled function
     * is invoked more than once during the `wait` timeout.
     *
     * If `wait` is `0` and `leading` is `false`, `func` invocation is deferred
     * until to the next tick, similar to `setTimeout` with a timeout of `0`.
     *
     * See [David Corbacho's article](https://css-tricks.com/debouncing-throttling-explained-examples/)
     * for details over the differences between `_.throttle` and `_.debounce`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Function
     * @param {Function} func The function to throttle.
     * @param {number} [wait=0] The number of milliseconds to throttle invocations to.
     * @param {Object} [options={}] The options object.
     * @param {boolean} [options.leading=true]
     *  Specify invoking on the leading edge of the timeout.
     * @param {boolean} [options.trailing=true]
     *  Specify invoking on the trailing edge of the timeout.
     * @returns {Function} Returns the new throttled function.
     * @example
     *
     * // Avoid excessively updating the position while scrolling.
     * jQuery(window).on('scroll', _.throttle(updatePosition, 100));
     *
     * // Invoke `renewToken` when the click event is fired, but not more than once every 5 minutes.
     * var throttled = _.throttle(renewToken, 300000, { 'trailing': false });
     * jQuery(element).on('click', throttled);
     *
     * // Cancel the trailing throttled invocation.
     * jQuery(window).on('popstate', throttled.cancel);
     */
    function throttle(func, wait, options) {
      var leading = true,
          trailing = true;

      if (typeof func != 'function') {
        throw new TypeError(FUNC_ERROR_TEXT);
      }
      if (isObject(options)) {
        leading = 'leading' in options ? !!options.leading : leading;
        trailing = 'trailing' in options ? !!options.trailing : trailing;
      }
      return debounce(func, wait, {
        'leading': leading,
        'maxWait': wait,
        'trailing': trailing
      });
    }

    /**
     * Creates a function that accepts up to one argument, ignoring any
     * additional arguments.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Function
     * @param {Function} func The function to cap arguments for.
     * @returns {Function} Returns the new capped function.
     * @example
     *
     * _.map(['6', '8', '10'], _.unary(parseInt));
     * // => [6, 8, 10]
     */
    function unary(func) {
      return ary(func, 1);
    }

    /**
     * Creates a function that provides `value` to `wrapper` as its first
     * argument. Any additional arguments provided to the function are appended
     * to those provided to the `wrapper`. The wrapper is invoked with the `this`
     * binding of the created function.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Function
     * @param {*} value The value to wrap.
     * @param {Function} [wrapper=identity] The wrapper function.
     * @returns {Function} Returns the new function.
     * @example
     *
     * var p = _.wrap(_.escape, function(func, text) {
     *   return '<p>' + func(text) + '</p>';
     * });
     *
     * p('fred, barney, & pebbles');
     * // => '<p>fred, barney, &amp; pebbles</p>'
     */
    function wrap(value, wrapper) {
      return partial(castFunction(wrapper), value);
    }

    /*------------------------------------------------------------------------*/

    /**
     * Casts `value` as an array if it's not one.
     *
     * @static
     * @memberOf _
     * @since 4.4.0
     * @category Lang
     * @param {*} value The value to inspect.
     * @returns {Array} Returns the cast array.
     * @example
     *
     * _.castArray(1);
     * // => [1]
     *
     * _.castArray({ 'a': 1 });
     * // => [{ 'a': 1 }]
     *
     * _.castArray('abc');
     * // => ['abc']
     *
     * _.castArray(null);
     * // => [null]
     *
     * _.castArray(undefined);
     * // => [undefined]
     *
     * _.castArray();
     * // => []
     *
     * var array = [1, 2, 3];
     * console.log(_.castArray(array) === array);
     * // => true
     */
    function castArray() {
      if (!arguments.length) {
        return [];
      }
      var value = arguments[0];
      return isArray(value) ? value : [value];
    }

    /**
     * Creates a shallow clone of `value`.
     *
     * **Note:** This method is loosely based on the
     * [structured clone algorithm](https://mdn.io/Structured_clone_algorithm)
     * and supports cloning arrays, array buffers, booleans, date objects, maps,
     * numbers, `Object` objects, regexes, sets, strings, symbols, and typed
     * arrays. The own enumerable properties of `arguments` objects are cloned
     * as plain objects. An empty object is returned for uncloneable values such
     * as error objects, functions, DOM nodes, and WeakMaps.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to clone.
     * @returns {*} Returns the cloned value.
     * @see _.cloneDeep
     * @example
     *
     * var objects = [{ 'a': 1 }, { 'b': 2 }];
     *
     * var shallow = _.clone(objects);
     * console.log(shallow[0] === objects[0]);
     * // => true
     */
    function clone(value) {
      return baseClone(value, CLONE_SYMBOLS_FLAG);
    }

    /**
     * This method is like `_.clone` except that it accepts `customizer` which
     * is invoked to produce the cloned value. If `customizer` returns `undefined`,
     * cloning is handled by the method instead. The `customizer` is invoked with
     * up to four arguments; (value [, index|key, object, stack]).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to clone.
     * @param {Function} [customizer] The function to customize cloning.
     * @returns {*} Returns the cloned value.
     * @see _.cloneDeepWith
     * @example
     *
     * function customizer(value) {
     *   if (_.isElement(value)) {
     *     return value.cloneNode(false);
     *   }
     * }
     *
     * var el = _.cloneWith(document.body, customizer);
     *
     * console.log(el === document.body);
     * // => false
     * console.log(el.nodeName);
     * // => 'BODY'
     * console.log(el.childNodes.length);
     * // => 0
     */
    function cloneWith(value, customizer) {
      customizer = typeof customizer == 'function' ? customizer : undefined;
      return baseClone(value, CLONE_SYMBOLS_FLAG, customizer);
    }

    /**
     * This method is like `_.clone` except that it recursively clones `value`.
     *
     * @static
     * @memberOf _
     * @since 1.0.0
     * @category Lang
     * @param {*} value The value to recursively clone.
     * @returns {*} Returns the deep cloned value.
     * @see _.clone
     * @example
     *
     * var objects = [{ 'a': 1 }, { 'b': 2 }];
     *
     * var deep = _.cloneDeep(objects);
     * console.log(deep[0] === objects[0]);
     * // => false
     */
    function cloneDeep(value) {
      return baseClone(value, CLONE_DEEP_FLAG | CLONE_SYMBOLS_FLAG);
    }

    /**
     * This method is like `_.cloneWith` except that it recursively clones `value`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to recursively clone.
     * @param {Function} [customizer] The function to customize cloning.
     * @returns {*} Returns the deep cloned value.
     * @see _.cloneWith
     * @example
     *
     * function customizer(value) {
     *   if (_.isElement(value)) {
     *     return value.cloneNode(true);
     *   }
     * }
     *
     * var el = _.cloneDeepWith(document.body, customizer);
     *
     * console.log(el === document.body);
     * // => false
     * console.log(el.nodeName);
     * // => 'BODY'
     * console.log(el.childNodes.length);
     * // => 20
     */
    function cloneDeepWith(value, customizer) {
      customizer = typeof customizer == 'function' ? customizer : undefined;
      return baseClone(value, CLONE_DEEP_FLAG | CLONE_SYMBOLS_FLAG, customizer);
    }

    /**
     * Checks if `object` conforms to `source` by invoking the predicate
     * properties of `source` with the corresponding property values of `object`.
     *
     * **Note:** This method is equivalent to `_.conforms` when `source` is
     * partially applied.
     *
     * @static
     * @memberOf _
     * @since 4.14.0
     * @category Lang
     * @param {Object} object The object to inspect.
     * @param {Object} source The object of property predicates to conform to.
     * @returns {boolean} Returns `true` if `object` conforms, else `false`.
     * @example
     *
     * var object = { 'a': 1, 'b': 2 };
     *
     * _.conformsTo(object, { 'b': function(n) { return n > 1; } });
     * // => true
     *
     * _.conformsTo(object, { 'b': function(n) { return n > 2; } });
     * // => false
     */
    function conformsTo(object, source) {
      return source == null || baseConformsTo(object, source, keys(source));
    }

    /**
     * Performs a
     * [`SameValueZero`](http://ecma-international.org/ecma-262/7.0/#sec-samevaluezero)
     * comparison between two values to determine if they are equivalent.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to compare.
     * @param {*} other The other value to compare.
     * @returns {boolean} Returns `true` if the values are equivalent, else `false`.
     * @example
     *
     * var object = { 'a': 1 };
     * var other = { 'a': 1 };
     *
     * _.eq(object, object);
     * // => true
     *
     * _.eq(object, other);
     * // => false
     *
     * _.eq('a', 'a');
     * // => true
     *
     * _.eq('a', Object('a'));
     * // => false
     *
     * _.eq(NaN, NaN);
     * // => true
     */
    function eq(value, other) {
      return value === other || (value !== value && other !== other);
    }

    /**
     * Checks if `value` is greater than `other`.
     *
     * @static
     * @memberOf _
     * @since 3.9.0
     * @category Lang
     * @param {*} value The value to compare.
     * @param {*} other The other value to compare.
     * @returns {boolean} Returns `true` if `value` is greater than `other`,
     *  else `false`.
     * @see _.lt
     * @example
     *
     * _.gt(3, 1);
     * // => true
     *
     * _.gt(3, 3);
     * // => false
     *
     * _.gt(1, 3);
     * // => false
     */
    var gt = createRelationalOperation(baseGt);

    /**
     * Checks if `value` is greater than or equal to `other`.
     *
     * @static
     * @memberOf _
     * @since 3.9.0
     * @category Lang
     * @param {*} value The value to compare.
     * @param {*} other The other value to compare.
     * @returns {boolean} Returns `true` if `value` is greater than or equal to
     *  `other`, else `false`.
     * @see _.lte
     * @example
     *
     * _.gte(3, 1);
     * // => true
     *
     * _.gte(3, 3);
     * // => true
     *
     * _.gte(1, 3);
     * // => false
     */
    var gte = createRelationalOperation(function(value, other) {
      return value >= other;
    });

    /**
     * Checks if `value` is likely an `arguments` object.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is an `arguments` object,
     *  else `false`.
     * @example
     *
     * _.isArguments(function() { return arguments; }());
     * // => true
     *
     * _.isArguments([1, 2, 3]);
     * // => false
     */
    var isArguments = baseIsArguments(function() { return arguments; }()) ? baseIsArguments : function(value) {
      return isObjectLike(value) && hasOwnProperty.call(value, 'callee') &&
        !propertyIsEnumerable.call(value, 'callee');
    };

    /**
     * Checks if `value` is classified as an `Array` object.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is an array, else `false`.
     * @example
     *
     * _.isArray([1, 2, 3]);
     * // => true
     *
     * _.isArray(document.body.children);
     * // => false
     *
     * _.isArray('abc');
     * // => false
     *
     * _.isArray(_.noop);
     * // => false
     */
    var isArray = Array.isArray;

    /**
     * Checks if `value` is classified as an `ArrayBuffer` object.
     *
     * @static
     * @memberOf _
     * @since 4.3.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is an array buffer, else `false`.
     * @example
     *
     * _.isArrayBuffer(new ArrayBuffer(2));
     * // => true
     *
     * _.isArrayBuffer(new Array(2));
     * // => false
     */
    var isArrayBuffer = nodeIsArrayBuffer ? baseUnary(nodeIsArrayBuffer) : baseIsArrayBuffer;

    /**
     * Checks if `value` is array-like. A value is considered array-like if it's
     * not a function and has a `value.length` that's an integer greater than or
     * equal to `0` and less than or equal to `Number.MAX_SAFE_INTEGER`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is array-like, else `false`.
     * @example
     *
     * _.isArrayLike([1, 2, 3]);
     * // => true
     *
     * _.isArrayLike(document.body.children);
     * // => true
     *
     * _.isArrayLike('abc');
     * // => true
     *
     * _.isArrayLike(_.noop);
     * // => false
     */
    function isArrayLike(value) {
      return value != null && isLength(value.length) && !isFunction(value);
    }

    /**
     * This method is like `_.isArrayLike` except that it also checks if `value`
     * is an object.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is an array-like object,
     *  else `false`.
     * @example
     *
     * _.isArrayLikeObject([1, 2, 3]);
     * // => true
     *
     * _.isArrayLikeObject(document.body.children);
     * // => true
     *
     * _.isArrayLikeObject('abc');
     * // => false
     *
     * _.isArrayLikeObject(_.noop);
     * // => false
     */
    function isArrayLikeObject(value) {
      return isObjectLike(value) && isArrayLike(value);
    }

    /**
     * Checks if `value` is classified as a boolean primitive or object.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a boolean, else `false`.
     * @example
     *
     * _.isBoolean(false);
     * // => true
     *
     * _.isBoolean(null);
     * // => false
     */
    function isBoolean(value) {
      return value === true || value === false ||
        (isObjectLike(value) && baseGetTag(value) == boolTag);
    }

    /**
     * Checks if `value` is a buffer.
     *
     * @static
     * @memberOf _
     * @since 4.3.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a buffer, else `false`.
     * @example
     *
     * _.isBuffer(new Buffer(2));
     * // => true
     *
     * _.isBuffer(new Uint8Array(2));
     * // => false
     */
    var isBuffer = nativeIsBuffer || stubFalse;

    /**
     * Checks if `value` is classified as a `Date` object.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a date object, else `false`.
     * @example
     *
     * _.isDate(new Date);
     * // => true
     *
     * _.isDate('Mon April 23 2012');
     * // => false
     */
    var isDate = nodeIsDate ? baseUnary(nodeIsDate) : baseIsDate;

    /**
     * Checks if `value` is likely a DOM element.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a DOM element, else `false`.
     * @example
     *
     * _.isElement(document.body);
     * // => true
     *
     * _.isElement('<body>');
     * // => false
     */
    function isElement(value) {
      return isObjectLike(value) && value.nodeType === 1 && !isPlainObject(value);
    }

    /**
     * Checks if `value` is an empty object, collection, map, or set.
     *
     * Objects are considered empty if they have no own enumerable string keyed
     * properties.
     *
     * Array-like values such as `arguments` objects, arrays, buffers, strings, or
     * jQuery-like collections are considered empty if they have a `length` of `0`.
     * Similarly, maps and sets are considered empty if they have a `size` of `0`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is empty, else `false`.
     * @example
     *
     * _.isEmpty(null);
     * // => true
     *
     * _.isEmpty(true);
     * // => true
     *
     * _.isEmpty(1);
     * // => true
     *
     * _.isEmpty([1, 2, 3]);
     * // => false
     *
     * _.isEmpty({ 'a': 1 });
     * // => false
     */
    function isEmpty(value) {
      if (value == null) {
        return true;
      }
      if (isArrayLike(value) &&
          (isArray(value) || typeof value == 'string' || typeof value.splice == 'function' ||
            isBuffer(value) || isTypedArray(value) || isArguments(value))) {
        return !value.length;
      }
      var tag = getTag(value);
      if (tag == mapTag || tag == setTag) {
        return !value.size;
      }
      if (isPrototype(value)) {
        return !baseKeys(value).length;
      }
      for (var key in value) {
        if (hasOwnProperty.call(value, key)) {
          return false;
        }
      }
      return true;
    }

    /**
     * Performs a deep comparison between two values to determine if they are
     * equivalent.
     *
     * **Note:** This method supports comparing arrays, array buffers, booleans,
     * date objects, error objects, maps, numbers, `Object` objects, regexes,
     * sets, strings, symbols, and typed arrays. `Object` objects are compared
     * by their own, not inherited, enumerable properties. Functions and DOM
     * nodes are compared by strict equality, i.e. `===`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to compare.
     * @param {*} other The other value to compare.
     * @returns {boolean} Returns `true` if the values are equivalent, else `false`.
     * @example
     *
     * var object = { 'a': 1 };
     * var other = { 'a': 1 };
     *
     * _.isEqual(object, other);
     * // => true
     *
     * object === other;
     * // => false
     */
    function isEqual(value, other) {
      return baseIsEqual(value, other);
    }

    /**
     * This method is like `_.isEqual` except that it accepts `customizer` which
     * is invoked to compare values. If `customizer` returns `undefined`, comparisons
     * are handled by the method instead. The `customizer` is invoked with up to
     * six arguments: (objValue, othValue [, index|key, object, other, stack]).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to compare.
     * @param {*} other The other value to compare.
     * @param {Function} [customizer] The function to customize comparisons.
     * @returns {boolean} Returns `true` if the values are equivalent, else `false`.
     * @example
     *
     * function isGreeting(value) {
     *   return /^h(?:i|ello)$/.test(value);
     * }
     *
     * function customizer(objValue, othValue) {
     *   if (isGreeting(objValue) && isGreeting(othValue)) {
     *     return true;
     *   }
     * }
     *
     * var array = ['hello', 'goodbye'];
     * var other = ['hi', 'goodbye'];
     *
     * _.isEqualWith(array, other, customizer);
     * // => true
     */
    function isEqualWith(value, other, customizer) {
      customizer = typeof customizer == 'function' ? customizer : undefined;
      var result = customizer ? customizer(value, other) : undefined;
      return result === undefined ? baseIsEqual(value, other, undefined, customizer) : !!result;
    }

    /**
     * Checks if `value` is an `Error`, `EvalError`, `RangeError`, `ReferenceError`,
     * `SyntaxError`, `TypeError`, or `URIError` object.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is an error object, else `false`.
     * @example
     *
     * _.isError(new Error);
     * // => true
     *
     * _.isError(Error);
     * // => false
     */
    function isError(value) {
      if (!isObjectLike(value)) {
        return false;
      }
      var tag = baseGetTag(value);
      return tag == errorTag || tag == domExcTag ||
        (typeof value.message == 'string' && typeof value.name == 'string' && !isPlainObject(value));
    }

    /**
     * Checks if `value` is a finite primitive number.
     *
     * **Note:** This method is based on
     * [`Number.isFinite`](https://mdn.io/Number/isFinite).
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a finite number, else `false`.
     * @example
     *
     * _.isFinite(3);
     * // => true
     *
     * _.isFinite(Number.MIN_VALUE);
     * // => true
     *
     * _.isFinite(Infinity);
     * // => false
     *
     * _.isFinite('3');
     * // => false
     */
    function isFinite(value) {
      return typeof value == 'number' && nativeIsFinite(value);
    }

    /**
     * Checks if `value` is classified as a `Function` object.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a function, else `false`.
     * @example
     *
     * _.isFunction(_);
     * // => true
     *
     * _.isFunction(/abc/);
     * // => false
     */
    function isFunction(value) {
      if (!isObject(value)) {
        return false;
      }
      // The use of `Object#toString` avoids issues with the `typeof` operator
      // in Safari 9 which returns 'object' for typed arrays and other constructors.
      var tag = baseGetTag(value);
      return tag == funcTag || tag == genTag || tag == asyncTag || tag == proxyTag;
    }

    /**
     * Checks if `value` is an integer.
     *
     * **Note:** This method is based on
     * [`Number.isInteger`](https://mdn.io/Number/isInteger).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is an integer, else `false`.
     * @example
     *
     * _.isInteger(3);
     * // => true
     *
     * _.isInteger(Number.MIN_VALUE);
     * // => false
     *
     * _.isInteger(Infinity);
     * // => false
     *
     * _.isInteger('3');
     * // => false
     */
    function isInteger(value) {
      return typeof value == 'number' && value == toInteger(value);
    }

    /**
     * Checks if `value` is a valid array-like length.
     *
     * **Note:** This method is loosely based on
     * [`ToLength`](http://ecma-international.org/ecma-262/7.0/#sec-tolength).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a valid length, else `false`.
     * @example
     *
     * _.isLength(3);
     * // => true
     *
     * _.isLength(Number.MIN_VALUE);
     * // => false
     *
     * _.isLength(Infinity);
     * // => false
     *
     * _.isLength('3');
     * // => false
     */
    function isLength(value) {
      return typeof value == 'number' &&
        value > -1 && value % 1 == 0 && value <= MAX_SAFE_INTEGER;
    }

    /**
     * Checks if `value` is the
     * [language type](http://www.ecma-international.org/ecma-262/7.0/#sec-ecmascript-language-types)
     * of `Object`. (e.g. arrays, functions, objects, regexes, `new Number(0)`, and `new String('')`)
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is an object, else `false`.
     * @example
     *
     * _.isObject({});
     * // => true
     *
     * _.isObject([1, 2, 3]);
     * // => true
     *
     * _.isObject(_.noop);
     * // => true
     *
     * _.isObject(null);
     * // => false
     */
    function isObject(value) {
      var type = typeof value;
      return value != null && (type == 'object' || type == 'function');
    }

    /**
     * Checks if `value` is object-like. A value is object-like if it's not `null`
     * and has a `typeof` result of "object".
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is object-like, else `false`.
     * @example
     *
     * _.isObjectLike({});
     * // => true
     *
     * _.isObjectLike([1, 2, 3]);
     * // => true
     *
     * _.isObjectLike(_.noop);
     * // => false
     *
     * _.isObjectLike(null);
     * // => false
     */
    function isObjectLike(value) {
      return value != null && typeof value == 'object';
    }

    /**
     * Checks if `value` is classified as a `Map` object.
     *
     * @static
     * @memberOf _
     * @since 4.3.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a map, else `false`.
     * @example
     *
     * _.isMap(new Map);
     * // => true
     *
     * _.isMap(new WeakMap);
     * // => false
     */
    var isMap = nodeIsMap ? baseUnary(nodeIsMap) : baseIsMap;

    /**
     * Performs a partial deep comparison between `object` and `source` to
     * determine if `object` contains equivalent property values.
     *
     * **Note:** This method is equivalent to `_.matches` when `source` is
     * partially applied.
     *
     * Partial comparisons will match empty array and empty object `source`
     * values against any array or object value, respectively. See `_.isEqual`
     * for a list of supported value comparisons.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Lang
     * @param {Object} object The object to inspect.
     * @param {Object} source The object of property values to match.
     * @returns {boolean} Returns `true` if `object` is a match, else `false`.
     * @example
     *
     * var object = { 'a': 1, 'b': 2 };
     *
     * _.isMatch(object, { 'b': 2 });
     * // => true
     *
     * _.isMatch(object, { 'b': 1 });
     * // => false
     */
    function isMatch(object, source) {
      return object === source || baseIsMatch(object, source, getMatchData(source));
    }

    /**
     * This method is like `_.isMatch` except that it accepts `customizer` which
     * is invoked to compare values. If `customizer` returns `undefined`, comparisons
     * are handled by the method instead. The `customizer` is invoked with five
     * arguments: (objValue, srcValue, index|key, object, source).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {Object} object The object to inspect.
     * @param {Object} source The object of property values to match.
     * @param {Function} [customizer] The function to customize comparisons.
     * @returns {boolean} Returns `true` if `object` is a match, else `false`.
     * @example
     *
     * function isGreeting(value) {
     *   return /^h(?:i|ello)$/.test(value);
     * }
     *
     * function customizer(objValue, srcValue) {
     *   if (isGreeting(objValue) && isGreeting(srcValue)) {
     *     return true;
     *   }
     * }
     *
     * var object = { 'greeting': 'hello' };
     * var source = { 'greeting': 'hi' };
     *
     * _.isMatchWith(object, source, customizer);
     * // => true
     */
    function isMatchWith(object, source, customizer) {
      customizer = typeof customizer == 'function' ? customizer : undefined;
      return baseIsMatch(object, source, getMatchData(source), customizer);
    }

    /**
     * Checks if `value` is `NaN`.
     *
     * **Note:** This method is based on
     * [`Number.isNaN`](https://mdn.io/Number/isNaN) and is not the same as
     * global [`isNaN`](https://mdn.io/isNaN) which returns `true` for
     * `undefined` and other non-number values.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is `NaN`, else `false`.
     * @example
     *
     * _.isNaN(NaN);
     * // => true
     *
     * _.isNaN(new Number(NaN));
     * // => true
     *
     * isNaN(undefined);
     * // => true
     *
     * _.isNaN(undefined);
     * // => false
     */
    function isNaN(value) {
      // An `NaN` primitive is the only value that is not equal to itself.
      // Perform the `toStringTag` check first to avoid errors with some
      // ActiveX objects in IE.
      return isNumber(value) && value != +value;
    }

    /**
     * Checks if `value` is a pristine native function.
     *
     * **Note:** This method can't reliably detect native functions in the presence
     * of the core-js package because core-js circumvents this kind of detection.
     * Despite multiple requests, the core-js maintainer has made it clear: any
     * attempt to fix the detection will be obstructed. As a result, we're left
     * with little choice but to throw an error. Unfortunately, this also affects
     * packages, like [babel-polyfill](https://www.npmjs.com/package/babel-polyfill),
     * which rely on core-js.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a native function,
     *  else `false`.
     * @example
     *
     * _.isNative(Array.prototype.push);
     * // => true
     *
     * _.isNative(_);
     * // => false
     */
    function isNative(value) {
      if (isMaskable(value)) {
        throw new Error(CORE_ERROR_TEXT);
      }
      return baseIsNative(value);
    }

    /**
     * Checks if `value` is `null`.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is `null`, else `false`.
     * @example
     *
     * _.isNull(null);
     * // => true
     *
     * _.isNull(void 0);
     * // => false
     */
    function isNull(value) {
      return value === null;
    }

    /**
     * Checks if `value` is `null` or `undefined`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is nullish, else `false`.
     * @example
     *
     * _.isNil(null);
     * // => true
     *
     * _.isNil(void 0);
     * // => true
     *
     * _.isNil(NaN);
     * // => false
     */
    function isNil(value) {
      return value == null;
    }

    /**
     * Checks if `value` is classified as a `Number` primitive or object.
     *
     * **Note:** To exclude `Infinity`, `-Infinity`, and `NaN`, which are
     * classified as numbers, use the `_.isFinite` method.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a number, else `false`.
     * @example
     *
     * _.isNumber(3);
     * // => true
     *
     * _.isNumber(Number.MIN_VALUE);
     * // => true
     *
     * _.isNumber(Infinity);
     * // => true
     *
     * _.isNumber('3');
     * // => false
     */
    function isNumber(value) {
      return typeof value == 'number' ||
        (isObjectLike(value) && baseGetTag(value) == numberTag);
    }

    /**
     * Checks if `value` is a plain object, that is, an object created by the
     * `Object` constructor or one with a `[[Prototype]]` of `null`.
     *
     * @static
     * @memberOf _
     * @since 0.8.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a plain object, else `false`.
     * @example
     *
     * function Foo() {
     *   this.a = 1;
     * }
     *
     * _.isPlainObject(new Foo);
     * // => false
     *
     * _.isPlainObject([1, 2, 3]);
     * // => false
     *
     * _.isPlainObject({ 'x': 0, 'y': 0 });
     * // => true
     *
     * _.isPlainObject(Object.create(null));
     * // => true
     */
    function isPlainObject(value) {
      if (!isObjectLike(value) || baseGetTag(value) != objectTag) {
        return false;
      }
      var proto = getPrototype(value);
      if (proto === null) {
        return true;
      }
      var Ctor = hasOwnProperty.call(proto, 'constructor') && proto.constructor;
      return typeof Ctor == 'function' && Ctor instanceof Ctor &&
        funcToString.call(Ctor) == objectCtorString;
    }

    /**
     * Checks if `value` is classified as a `RegExp` object.
     *
     * @static
     * @memberOf _
     * @since 0.1.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a regexp, else `false`.
     * @example
     *
     * _.isRegExp(/abc/);
     * // => true
     *
     * _.isRegExp('/abc/');
     * // => false
     */
    var isRegExp = nodeIsRegExp ? baseUnary(nodeIsRegExp) : baseIsRegExp;

    /**
     * Checks if `value` is a safe integer. An integer is safe if it's an IEEE-754
     * double precision number which isn't the result of a rounded unsafe integer.
     *
     * **Note:** This method is based on
     * [`Number.isSafeInteger`](https://mdn.io/Number/isSafeInteger).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a safe integer, else `false`.
     * @example
     *
     * _.isSafeInteger(3);
     * // => true
     *
     * _.isSafeInteger(Number.MIN_VALUE);
     * // => false
     *
     * _.isSafeInteger(Infinity);
     * // => false
     *
     * _.isSafeInteger('3');
     * // => false
     */
    function isSafeInteger(value) {
      return isInteger(value) && value >= -MAX_SAFE_INTEGER && value <= MAX_SAFE_INTEGER;
    }

    /**
     * Checks if `value` is classified as a `Set` object.
     *
     * @static
     * @memberOf _
     * @since 4.3.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a set, else `false`.
     * @example
     *
     * _.isSet(new Set);
     * // => true
     *
     * _.isSet(new WeakSet);
     * // => false
     */
    var isSet = nodeIsSet ? baseUnary(nodeIsSet) : baseIsSet;

    /**
     * Checks if `value` is classified as a `String` primitive or object.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a string, else `false`.
     * @example
     *
     * _.isString('abc');
     * // => true
     *
     * _.isString(1);
     * // => false
     */
    function isString(value) {
      return typeof value == 'string' ||
        (!isArray(value) && isObjectLike(value) && baseGetTag(value) == stringTag);
    }

    /**
     * Checks if `value` is classified as a `Symbol` primitive or object.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a symbol, else `false`.
     * @example
     *
     * _.isSymbol(Symbol.iterator);
     * // => true
     *
     * _.isSymbol('abc');
     * // => false
     */
    function isSymbol(value) {
      return typeof value == 'symbol' ||
        (isObjectLike(value) && baseGetTag(value) == symbolTag);
    }

    /**
     * Checks if `value` is classified as a typed array.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a typed array, else `false`.
     * @example
     *
     * _.isTypedArray(new Uint8Array);
     * // => true
     *
     * _.isTypedArray([]);
     * // => false
     */
    var isTypedArray = nodeIsTypedArray ? baseUnary(nodeIsTypedArray) : baseIsTypedArray;

    /**
     * Checks if `value` is `undefined`.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is `undefined`, else `false`.
     * @example
     *
     * _.isUndefined(void 0);
     * // => true
     *
     * _.isUndefined(null);
     * // => false
     */
    function isUndefined(value) {
      return value === undefined;
    }

    /**
     * Checks if `value` is classified as a `WeakMap` object.
     *
     * @static
     * @memberOf _
     * @since 4.3.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a weak map, else `false`.
     * @example
     *
     * _.isWeakMap(new WeakMap);
     * // => true
     *
     * _.isWeakMap(new Map);
     * // => false
     */
    function isWeakMap(value) {
      return isObjectLike(value) && getTag(value) == weakMapTag;
    }

    /**
     * Checks if `value` is classified as a `WeakSet` object.
     *
     * @static
     * @memberOf _
     * @since 4.3.0
     * @category Lang
     * @param {*} value The value to check.
     * @returns {boolean} Returns `true` if `value` is a weak set, else `false`.
     * @example
     *
     * _.isWeakSet(new WeakSet);
     * // => true
     *
     * _.isWeakSet(new Set);
     * // => false
     */
    function isWeakSet(value) {
      return isObjectLike(value) && baseGetTag(value) == weakSetTag;
    }

    /**
     * Checks if `value` is less than `other`.
     *
     * @static
     * @memberOf _
     * @since 3.9.0
     * @category Lang
     * @param {*} value The value to compare.
     * @param {*} other The other value to compare.
     * @returns {boolean} Returns `true` if `value` is less than `other`,
     *  else `false`.
     * @see _.gt
     * @example
     *
     * _.lt(1, 3);
     * // => true
     *
     * _.lt(3, 3);
     * // => false
     *
     * _.lt(3, 1);
     * // => false
     */
    var lt = createRelationalOperation(baseLt);

    /**
     * Checks if `value` is less than or equal to `other`.
     *
     * @static
     * @memberOf _
     * @since 3.9.0
     * @category Lang
     * @param {*} value The value to compare.
     * @param {*} other The other value to compare.
     * @returns {boolean} Returns `true` if `value` is less than or equal to
     *  `other`, else `false`.
     * @see _.gte
     * @example
     *
     * _.lte(1, 3);
     * // => true
     *
     * _.lte(3, 3);
     * // => true
     *
     * _.lte(3, 1);
     * // => false
     */
    var lte = createRelationalOperation(function(value, other) {
      return value <= other;
    });

    /**
     * Converts `value` to an array.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Lang
     * @param {*} value The value to convert.
     * @returns {Array} Returns the converted array.
     * @example
     *
     * _.toArray({ 'a': 1, 'b': 2 });
     * // => [1, 2]
     *
     * _.toArray('abc');
     * // => ['a', 'b', 'c']
     *
     * _.toArray(1);
     * // => []
     *
     * _.toArray(null);
     * // => []
     */
    function toArray(value) {
      if (!value) {
        return [];
      }
      if (isArrayLike(value)) {
        return isString(value) ? stringToArray(value) : copyArray(value);
      }
      if (symIterator && value[symIterator]) {
        return iteratorToArray(value[symIterator]());
      }
      var tag = getTag(value),
          func = tag == mapTag ? mapToArray : (tag == setTag ? setToArray : values);

      return func(value);
    }

    /**
     * Converts `value` to a finite number.
     *
     * @static
     * @memberOf _
     * @since 4.12.0
     * @category Lang
     * @param {*} value The value to convert.
     * @returns {number} Returns the converted number.
     * @example
     *
     * _.toFinite(3.2);
     * // => 3.2
     *
     * _.toFinite(Number.MIN_VALUE);
     * // => 5e-324
     *
     * _.toFinite(Infinity);
     * // => 1.7976931348623157e+308
     *
     * _.toFinite('3.2');
     * // => 3.2
     */
    function toFinite(value) {
      if (!value) {
        return value === 0 ? value : 0;
      }
      value = toNumber(value);
      if (value === INFINITY || value === -INFINITY) {
        var sign = (value < 0 ? -1 : 1);
        return sign * MAX_INTEGER;
      }
      return value === value ? value : 0;
    }

    /**
     * Converts `value` to an integer.
     *
     * **Note:** This method is loosely based on
     * [`ToInteger`](http://www.ecma-international.org/ecma-262/7.0/#sec-tointeger).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to convert.
     * @returns {number} Returns the converted integer.
     * @example
     *
     * _.toInteger(3.2);
     * // => 3
     *
     * _.toInteger(Number.MIN_VALUE);
     * // => 0
     *
     * _.toInteger(Infinity);
     * // => 1.7976931348623157e+308
     *
     * _.toInteger('3.2');
     * // => 3
     */
    function toInteger(value) {
      var result = toFinite(value),
          remainder = result % 1;

      return result === result ? (remainder ? result - remainder : result) : 0;
    }

    /**
     * Converts `value` to an integer suitable for use as the length of an
     * array-like object.
     *
     * **Note:** This method is based on
     * [`ToLength`](http://ecma-international.org/ecma-262/7.0/#sec-tolength).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to convert.
     * @returns {number} Returns the converted integer.
     * @example
     *
     * _.toLength(3.2);
     * // => 3
     *
     * _.toLength(Number.MIN_VALUE);
     * // => 0
     *
     * _.toLength(Infinity);
     * // => 4294967295
     *
     * _.toLength('3.2');
     * // => 3
     */
    function toLength(value) {
      return value ? baseClamp(toInteger(value), 0, MAX_ARRAY_LENGTH) : 0;
    }

    /**
     * Converts `value` to a number.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to process.
     * @returns {number} Returns the number.
     * @example
     *
     * _.toNumber(3.2);
     * // => 3.2
     *
     * _.toNumber(Number.MIN_VALUE);
     * // => 5e-324
     *
     * _.toNumber(Infinity);
     * // => Infinity
     *
     * _.toNumber('3.2');
     * // => 3.2
     */
    function toNumber(value) {
      if (typeof value == 'number') {
        return value;
      }
      if (isSymbol(value)) {
        return NAN;
      }
      if (isObject(value)) {
        var other = typeof value.valueOf == 'function' ? value.valueOf() : value;
        value = isObject(other) ? (other + '') : other;
      }
      if (typeof value != 'string') {
        return value === 0 ? value : +value;
      }
      value = baseTrim(value);
      var isBinary = reIsBinary.test(value);
      return (isBinary || reIsOctal.test(value))
        ? freeParseInt(value.slice(2), isBinary ? 2 : 8)
        : (reIsBadHex.test(value) ? NAN : +value);
    }

    /**
     * Converts `value` to a plain object flattening inherited enumerable string
     * keyed properties of `value` to own properties of the plain object.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Lang
     * @param {*} value The value to convert.
     * @returns {Object} Returns the converted plain object.
     * @example
     *
     * function Foo() {
     *   this.b = 2;
     * }
     *
     * Foo.prototype.c = 3;
     *
     * _.assign({ 'a': 1 }, new Foo);
     * // => { 'a': 1, 'b': 2 }
     *
     * _.assign({ 'a': 1 }, _.toPlainObject(new Foo));
     * // => { 'a': 1, 'b': 2, 'c': 3 }
     */
    function toPlainObject(value) {
      return copyObject(value, keysIn(value));
    }

    /**
     * Converts `value` to a safe integer. A safe integer can be compared and
     * represented correctly.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to convert.
     * @returns {number} Returns the converted integer.
     * @example
     *
     * _.toSafeInteger(3.2);
     * // => 3
     *
     * _.toSafeInteger(Number.MIN_VALUE);
     * // => 0
     *
     * _.toSafeInteger(Infinity);
     * // => 9007199254740991
     *
     * _.toSafeInteger('3.2');
     * // => 3
     */
    function toSafeInteger(value) {
      return value
        ? baseClamp(toInteger(value), -MAX_SAFE_INTEGER, MAX_SAFE_INTEGER)
        : (value === 0 ? value : 0);
    }

    /**
     * Converts `value` to a string. An empty string is returned for `null`
     * and `undefined` values. The sign of `-0` is preserved.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Lang
     * @param {*} value The value to convert.
     * @returns {string} Returns the converted string.
     * @example
     *
     * _.toString(null);
     * // => ''
     *
     * _.toString(-0);
     * // => '-0'
     *
     * _.toString([1, 2, 3]);
     * // => '1,2,3'
     */
    function toString(value) {
      return value == null ? '' : baseToString(value);
    }

    /*------------------------------------------------------------------------*/

    /**
     * Assigns own enumerable string keyed properties of source objects to the
     * destination object. Source objects are applied from left to right.
     * Subsequent sources overwrite property assignments of previous sources.
     *
     * **Note:** This method mutates `object` and is loosely based on
     * [`Object.assign`](https://mdn.io/Object/assign).
     *
     * @static
     * @memberOf _
     * @since 0.10.0
     * @category Object
     * @param {Object} object The destination object.
     * @param {...Object} [sources] The source objects.
     * @returns {Object} Returns `object`.
     * @see _.assignIn
     * @example
     *
     * function Foo() {
     *   this.a = 1;
     * }
     *
     * function Bar() {
     *   this.c = 3;
     * }
     *
     * Foo.prototype.b = 2;
     * Bar.prototype.d = 4;
     *
     * _.assign({ 'a': 0 }, new Foo, new Bar);
     * // => { 'a': 1, 'c': 3 }
     */
    var assign = createAssigner(function(object, source) {
      if (isPrototype(source) || isArrayLike(source)) {
        copyObject(source, keys(source), object);
        return;
      }
      for (var key in source) {
        if (hasOwnProperty.call(source, key)) {
          assignValue(object, key, source[key]);
        }
      }
    });

    /**
     * This method is like `_.assign` except that it iterates over own and
     * inherited source properties.
     *
     * **Note:** This method mutates `object`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @alias extend
     * @category Object
     * @param {Object} object The destination object.
     * @param {...Object} [sources] The source objects.
     * @returns {Object} Returns `object`.
     * @see _.assign
     * @example
     *
     * function Foo() {
     *   this.a = 1;
     * }
     *
     * function Bar() {
     *   this.c = 3;
     * }
     *
     * Foo.prototype.b = 2;
     * Bar.prototype.d = 4;
     *
     * _.assignIn({ 'a': 0 }, new Foo, new Bar);
     * // => { 'a': 1, 'b': 2, 'c': 3, 'd': 4 }
     */
    var assignIn = createAssigner(function(object, source) {
      copyObject(source, keysIn(source), object);
    });

    /**
     * This method is like `_.assignIn` except that it accepts `customizer`
     * which is invoked to produce the assigned values. If `customizer` returns
     * `undefined`, assignment is handled by the method instead. The `customizer`
     * is invoked with five arguments: (objValue, srcValue, key, object, source).
     *
     * **Note:** This method mutates `object`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @alias extendWith
     * @category Object
     * @param {Object} object The destination object.
     * @param {...Object} sources The source objects.
     * @param {Function} [customizer] The function to customize assigned values.
     * @returns {Object} Returns `object`.
     * @see _.assignWith
     * @example
     *
     * function customizer(objValue, srcValue) {
     *   return _.isUndefined(objValue) ? srcValue : objValue;
     * }
     *
     * var defaults = _.partialRight(_.assignInWith, customizer);
     *
     * defaults({ 'a': 1 }, { 'b': 2 }, { 'a': 3 });
     * // => { 'a': 1, 'b': 2 }
     */
    var assignInWith = createAssigner(function(object, source, srcIndex, customizer) {
      copyObject(source, keysIn(source), object, customizer);
    });

    /**
     * This method is like `_.assign` except that it accepts `customizer`
     * which is invoked to produce the assigned values. If `customizer` returns
     * `undefined`, assignment is handled by the method instead. The `customizer`
     * is invoked with five arguments: (objValue, srcValue, key, object, source).
     *
     * **Note:** This method mutates `object`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Object
     * @param {Object} object The destination object.
     * @param {...Object} sources The source objects.
     * @param {Function} [customizer] The function to customize assigned values.
     * @returns {Object} Returns `object`.
     * @see _.assignInWith
     * @example
     *
     * function customizer(objValue, srcValue) {
     *   return _.isUndefined(objValue) ? srcValue : objValue;
     * }
     *
     * var defaults = _.partialRight(_.assignWith, customizer);
     *
     * defaults({ 'a': 1 }, { 'b': 2 }, { 'a': 3 });
     * // => { 'a': 1, 'b': 2 }
     */
    var assignWith = createAssigner(function(object, source, srcIndex, customizer) {
      copyObject(source, keys(source), object, customizer);
    });

    /**
     * Creates an array of values corresponding to `paths` of `object`.
     *
     * @static
     * @memberOf _
     * @since 1.0.0
     * @category Object
     * @param {Object} object The object to iterate over.
     * @param {...(string|string[])} [paths] The property paths to pick.
     * @returns {Array} Returns the picked values.
     * @example
     *
     * var object = { 'a': [{ 'b': { 'c': 3 } }, 4] };
     *
     * _.at(object, ['a[0].b.c', 'a[1]']);
     * // => [3, 4]
     */
    var at = flatRest(baseAt);

    /**
     * Creates an object that inherits from the `prototype` object. If a
     * `properties` object is given, its own enumerable string keyed properties
     * are assigned to the created object.
     *
     * @static
     * @memberOf _
     * @since 2.3.0
     * @category Object
     * @param {Object} prototype The object to inherit from.
     * @param {Object} [properties] The properties to assign to the object.
     * @returns {Object} Returns the new object.
     * @example
     *
     * function Shape() {
     *   this.x = 0;
     *   this.y = 0;
     * }
     *
     * function Circle() {
     *   Shape.call(this);
     * }
     *
     * Circle.prototype = _.create(Shape.prototype, {
     *   'constructor': Circle
     * });
     *
     * var circle = new Circle;
     * circle instanceof Circle;
     * // => true
     *
     * circle instanceof Shape;
     * // => true
     */
    function create(prototype, properties) {
      var result = baseCreate(prototype);
      return properties == null ? result : baseAssign(result, properties);
    }

    /**
     * Assigns own and inherited enumerable string keyed properties of source
     * objects to the destination object for all destination properties that
     * resolve to `undefined`. Source objects are applied from left to right.
     * Once a property is set, additional values of the same property are ignored.
     *
     * **Note:** This method mutates `object`.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Object
     * @param {Object} object The destination object.
     * @param {...Object} [sources] The source objects.
     * @returns {Object} Returns `object`.
     * @see _.defaultsDeep
     * @example
     *
     * _.defaults({ 'a': 1 }, { 'b': 2 }, { 'a': 3 });
     * // => { 'a': 1, 'b': 2 }
     */
    var defaults = baseRest(function(object, sources) {
      object = Object(object);

      var index = -1;
      var length = sources.length;
      var guard = length > 2 ? sources[2] : undefined;

      if (guard && isIterateeCall(sources[0], sources[1], guard)) {
        length = 1;
      }

      while (++index < length) {
        var source = sources[index];
        var props = keysIn(source);
        var propsIndex = -1;
        var propsLength = props.length;

        while (++propsIndex < propsLength) {
          var key = props[propsIndex];
          var value = object[key];

          if (value === undefined ||
              (eq(value, objectProto[key]) && !hasOwnProperty.call(object, key))) {
            object[key] = source[key];
          }
        }
      }

      return object;
    });

    /**
     * This method is like `_.defaults` except that it recursively assigns
     * default properties.
     *
     * **Note:** This method mutates `object`.
     *
     * @static
     * @memberOf _
     * @since 3.10.0
     * @category Object
     * @param {Object} object The destination object.
     * @param {...Object} [sources] The source objects.
     * @returns {Object} Returns `object`.
     * @see _.defaults
     * @example
     *
     * _.defaultsDeep({ 'a': { 'b': 2 } }, { 'a': { 'b': 1, 'c': 3 } });
     * // => { 'a': { 'b': 2, 'c': 3 } }
     */
    var defaultsDeep = baseRest(function(args) {
      args.push(undefined, customDefaultsMerge);
      return apply(mergeWith, undefined, args);
    });

    /**
     * This method is like `_.find` except that it returns the key of the first
     * element `predicate` returns truthy for instead of the element itself.
     *
     * @static
     * @memberOf _
     * @since 1.1.0
     * @category Object
     * @param {Object} object The object to inspect.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @returns {string|undefined} Returns the key of the matched element,
     *  else `undefined`.
     * @example
     *
     * var users = {
     *   'barney':  { 'age': 36, 'active': true },
     *   'fred':    { 'age': 40, 'active': false },
     *   'pebbles': { 'age': 1,  'active': true }
     * };
     *
     * _.findKey(users, function(o) { return o.age < 40; });
     * // => 'barney' (iteration order is not guaranteed)
     *
     * // The `_.matches` iteratee shorthand.
     * _.findKey(users, { 'age': 1, 'active': true });
     * // => 'pebbles'
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.findKey(users, ['active', false]);
     * // => 'fred'
     *
     * // The `_.property` iteratee shorthand.
     * _.findKey(users, 'active');
     * // => 'barney'
     */
    function findKey(object, predicate) {
      return baseFindKey(object, getIteratee(predicate, 3), baseForOwn);
    }

    /**
     * This method is like `_.findKey` except that it iterates over elements of
     * a collection in the opposite order.
     *
     * @static
     * @memberOf _
     * @since 2.0.0
     * @category Object
     * @param {Object} object The object to inspect.
     * @param {Function} [predicate=_.identity] The function invoked per iteration.
     * @returns {string|undefined} Returns the key of the matched element,
     *  else `undefined`.
     * @example
     *
     * var users = {
     *   'barney':  { 'age': 36, 'active': true },
     *   'fred':    { 'age': 40, 'active': false },
     *   'pebbles': { 'age': 1,  'active': true }
     * };
     *
     * _.findLastKey(users, function(o) { return o.age < 40; });
     * // => returns 'pebbles' assuming `_.findKey` returns 'barney'
     *
     * // The `_.matches` iteratee shorthand.
     * _.findLastKey(users, { 'age': 36, 'active': true });
     * // => 'barney'
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.findLastKey(users, ['active', false]);
     * // => 'fred'
     *
     * // The `_.property` iteratee shorthand.
     * _.findLastKey(users, 'active');
     * // => 'pebbles'
     */
    function findLastKey(object, predicate) {
      return baseFindKey(object, getIteratee(predicate, 3), baseForOwnRight);
    }

    /**
     * Iterates over own and inherited enumerable string keyed properties of an
     * object and invokes `iteratee` for each property. The iteratee is invoked
     * with three arguments: (value, key, object). Iteratee functions may exit
     * iteration early by explicitly returning `false`.
     *
     * @static
     * @memberOf _
     * @since 0.3.0
     * @category Object
     * @param {Object} object The object to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @returns {Object} Returns `object`.
     * @see _.forInRight
     * @example
     *
     * function Foo() {
     *   this.a = 1;
     *   this.b = 2;
     * }
     *
     * Foo.prototype.c = 3;
     *
     * _.forIn(new Foo, function(value, key) {
     *   console.log(key);
     * });
     * // => Logs 'a', 'b', then 'c' (iteration order is not guaranteed).
     */
    function forIn(object, iteratee) {
      return object == null
        ? object
        : baseFor(object, getIteratee(iteratee, 3), keysIn);
    }

    /**
     * This method is like `_.forIn` except that it iterates over properties of
     * `object` in the opposite order.
     *
     * @static
     * @memberOf _
     * @since 2.0.0
     * @category Object
     * @param {Object} object The object to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @returns {Object} Returns `object`.
     * @see _.forIn
     * @example
     *
     * function Foo() {
     *   this.a = 1;
     *   this.b = 2;
     * }
     *
     * Foo.prototype.c = 3;
     *
     * _.forInRight(new Foo, function(value, key) {
     *   console.log(key);
     * });
     * // => Logs 'c', 'b', then 'a' assuming `_.forIn` logs 'a', 'b', then 'c'.
     */
    function forInRight(object, iteratee) {
      return object == null
        ? object
        : baseForRight(object, getIteratee(iteratee, 3), keysIn);
    }

    /**
     * Iterates over own enumerable string keyed properties of an object and
     * invokes `iteratee` for each property. The iteratee is invoked with three
     * arguments: (value, key, object). Iteratee functions may exit iteration
     * early by explicitly returning `false`.
     *
     * @static
     * @memberOf _
     * @since 0.3.0
     * @category Object
     * @param {Object} object The object to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @returns {Object} Returns `object`.
     * @see _.forOwnRight
     * @example
     *
     * function Foo() {
     *   this.a = 1;
     *   this.b = 2;
     * }
     *
     * Foo.prototype.c = 3;
     *
     * _.forOwn(new Foo, function(value, key) {
     *   console.log(key);
     * });
     * // => Logs 'a' then 'b' (iteration order is not guaranteed).
     */
    function forOwn(object, iteratee) {
      return object && baseForOwn(object, getIteratee(iteratee, 3));
    }

    /**
     * This method is like `_.forOwn` except that it iterates over properties of
     * `object` in the opposite order.
     *
     * @static
     * @memberOf _
     * @since 2.0.0
     * @category Object
     * @param {Object} object The object to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @returns {Object} Returns `object`.
     * @see _.forOwn
     * @example
     *
     * function Foo() {
     *   this.a = 1;
     *   this.b = 2;
     * }
     *
     * Foo.prototype.c = 3;
     *
     * _.forOwnRight(new Foo, function(value, key) {
     *   console.log(key);
     * });
     * // => Logs 'b' then 'a' assuming `_.forOwn` logs 'a' then 'b'.
     */
    function forOwnRight(object, iteratee) {
      return object && baseForOwnRight(object, getIteratee(iteratee, 3));
    }

    /**
     * Creates an array of function property names from own enumerable properties
     * of `object`.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Object
     * @param {Object} object The object to inspect.
     * @returns {Array} Returns the function names.
     * @see _.functionsIn
     * @example
     *
     * function Foo() {
     *   this.a = _.constant('a');
     *   this.b = _.constant('b');
     * }
     *
     * Foo.prototype.c = _.constant('c');
     *
     * _.functions(new Foo);
     * // => ['a', 'b']
     */
    function functions(object) {
      return object == null ? [] : baseFunctions(object, keys(object));
    }

    /**
     * Creates an array of function property names from own and inherited
     * enumerable properties of `object`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Object
     * @param {Object} object The object to inspect.
     * @returns {Array} Returns the function names.
     * @see _.functions
     * @example
     *
     * function Foo() {
     *   this.a = _.constant('a');
     *   this.b = _.constant('b');
     * }
     *
     * Foo.prototype.c = _.constant('c');
     *
     * _.functionsIn(new Foo);
     * // => ['a', 'b', 'c']
     */
    function functionsIn(object) {
      return object == null ? [] : baseFunctions(object, keysIn(object));
    }

    /**
     * Gets the value at `path` of `object`. If the resolved value is
     * `undefined`, the `defaultValue` is returned in its place.
     *
     * @static
     * @memberOf _
     * @since 3.7.0
     * @category Object
     * @param {Object} object The object to query.
     * @param {Array|string} path The path of the property to get.
     * @param {*} [defaultValue] The value returned for `undefined` resolved values.
     * @returns {*} Returns the resolved value.
     * @example
     *
     * var object = { 'a': [{ 'b': { 'c': 3 } }] };
     *
     * _.get(object, 'a[0].b.c');
     * // => 3
     *
     * _.get(object, ['a', '0', 'b', 'c']);
     * // => 3
     *
     * _.get(object, 'a.b.c', 'default');
     * // => 'default'
     */
    function get(object, path, defaultValue) {
      var result = object == null ? undefined : baseGet(object, path);
      return result === undefined ? defaultValue : result;
    }

    /**
     * Checks if `path` is a direct property of `object`.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Object
     * @param {Object} object The object to query.
     * @param {Array|string} path The path to check.
     * @returns {boolean} Returns `true` if `path` exists, else `false`.
     * @example
     *
     * var object = { 'a': { 'b': 2 } };
     * var other = _.create({ 'a': _.create({ 'b': 2 }) });
     *
     * _.has(object, 'a');
     * // => true
     *
     * _.has(object, 'a.b');
     * // => true
     *
     * _.has(object, ['a', 'b']);
     * // => true
     *
     * _.has(other, 'a');
     * // => false
     */
    function has(object, path) {
      return object != null && hasPath(object, path, baseHas);
    }

    /**
     * Checks if `path` is a direct or inherited property of `object`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Object
     * @param {Object} object The object to query.
     * @param {Array|string} path The path to check.
     * @returns {boolean} Returns `true` if `path` exists, else `false`.
     * @example
     *
     * var object = _.create({ 'a': _.create({ 'b': 2 }) });
     *
     * _.hasIn(object, 'a');
     * // => true
     *
     * _.hasIn(object, 'a.b');
     * // => true
     *
     * _.hasIn(object, ['a', 'b']);
     * // => true
     *
     * _.hasIn(object, 'b');
     * // => false
     */
    function hasIn(object, path) {
      return object != null && hasPath(object, path, baseHasIn);
    }

    /**
     * Creates an object composed of the inverted keys and values of `object`.
     * If `object` contains duplicate values, subsequent values overwrite
     * property assignments of previous values.
     *
     * @static
     * @memberOf _
     * @since 0.7.0
     * @category Object
     * @param {Object} object The object to invert.
     * @returns {Object} Returns the new inverted object.
     * @example
     *
     * var object = { 'a': 1, 'b': 2, 'c': 1 };
     *
     * _.invert(object);
     * // => { '1': 'c', '2': 'b' }
     */
    var invert = createInverter(function(result, value, key) {
      if (value != null &&
          typeof value.toString != 'function') {
        value = nativeObjectToString.call(value);
      }

      result[value] = key;
    }, constant(identity));

    /**
     * This method is like `_.invert` except that the inverted object is generated
     * from the results of running each element of `object` thru `iteratee`. The
     * corresponding inverted value of each inverted key is an array of keys
     * responsible for generating the inverted value. The iteratee is invoked
     * with one argument: (value).
     *
     * @static
     * @memberOf _
     * @since 4.1.0
     * @category Object
     * @param {Object} object The object to invert.
     * @param {Function} [iteratee=_.identity] The iteratee invoked per element.
     * @returns {Object} Returns the new inverted object.
     * @example
     *
     * var object = { 'a': 1, 'b': 2, 'c': 1 };
     *
     * _.invertBy(object);
     * // => { '1': ['a', 'c'], '2': ['b'] }
     *
     * _.invertBy(object, function(value) {
     *   return 'group' + value;
     * });
     * // => { 'group1': ['a', 'c'], 'group2': ['b'] }
     */
    var invertBy = createInverter(function(result, value, key) {
      if (value != null &&
          typeof value.toString != 'function') {
        value = nativeObjectToString.call(value);
      }

      if (hasOwnProperty.call(result, value)) {
        result[value].push(key);
      } else {
        result[value] = [key];
      }
    }, getIteratee);

    /**
     * Invokes the method at `path` of `object`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Object
     * @param {Object} object The object to query.
     * @param {Array|string} path The path of the method to invoke.
     * @param {...*} [args] The arguments to invoke the method with.
     * @returns {*} Returns the result of the invoked method.
     * @example
     *
     * var object = { 'a': [{ 'b': { 'c': [1, 2, 3, 4] } }] };
     *
     * _.invoke(object, 'a[0].b.c.slice', 1, 3);
     * // => [2, 3]
     */
    var invoke = baseRest(baseInvoke);

    /**
     * Creates an array of the own enumerable property names of `object`.
     *
     * **Note:** Non-object values are coerced to objects. See the
     * [ES spec](http://ecma-international.org/ecma-262/7.0/#sec-object.keys)
     * for more details.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Object
     * @param {Object} object The object to query.
     * @returns {Array} Returns the array of property names.
     * @example
     *
     * function Foo() {
     *   this.a = 1;
     *   this.b = 2;
     * }
     *
     * Foo.prototype.c = 3;
     *
     * _.keys(new Foo);
     * // => ['a', 'b'] (iteration order is not guaranteed)
     *
     * _.keys('hi');
     * // => ['0', '1']
     */
    function keys(object) {
      return isArrayLike(object) ? arrayLikeKeys(object) : baseKeys(object);
    }

    /**
     * Creates an array of the own and inherited enumerable property names of `object`.
     *
     * **Note:** Non-object values are coerced to objects.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Object
     * @param {Object} object The object to query.
     * @returns {Array} Returns the array of property names.
     * @example
     *
     * function Foo() {
     *   this.a = 1;
     *   this.b = 2;
     * }
     *
     * Foo.prototype.c = 3;
     *
     * _.keysIn(new Foo);
     * // => ['a', 'b', 'c'] (iteration order is not guaranteed)
     */
    function keysIn(object) {
      return isArrayLike(object) ? arrayLikeKeys(object, true) : baseKeysIn(object);
    }

    /**
     * The opposite of `_.mapValues`; this method creates an object with the
     * same values as `object` and keys generated by running each own enumerable
     * string keyed property of `object` thru `iteratee`. The iteratee is invoked
     * with three arguments: (value, key, object).
     *
     * @static
     * @memberOf _
     * @since 3.8.0
     * @category Object
     * @param {Object} object The object to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @returns {Object} Returns the new mapped object.
     * @see _.mapValues
     * @example
     *
     * _.mapKeys({ 'a': 1, 'b': 2 }, function(value, key) {
     *   return key + value;
     * });
     * // => { 'a1': 1, 'b2': 2 }
     */
    function mapKeys(object, iteratee) {
      var result = {};
      iteratee = getIteratee(iteratee, 3);

      baseForOwn(object, function(value, key, object) {
        baseAssignValue(result, iteratee(value, key, object), value);
      });
      return result;
    }

    /**
     * Creates an object with the same keys as `object` and values generated
     * by running each own enumerable string keyed property of `object` thru
     * `iteratee`. The iteratee is invoked with three arguments:
     * (value, key, object).
     *
     * @static
     * @memberOf _
     * @since 2.4.0
     * @category Object
     * @param {Object} object The object to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @returns {Object} Returns the new mapped object.
     * @see _.mapKeys
     * @example
     *
     * var users = {
     *   'fred':    { 'user': 'fred',    'age': 40 },
     *   'pebbles': { 'user': 'pebbles', 'age': 1 }
     * };
     *
     * _.mapValues(users, function(o) { return o.age; });
     * // => { 'fred': 40, 'pebbles': 1 } (iteration order is not guaranteed)
     *
     * // The `_.property` iteratee shorthand.
     * _.mapValues(users, 'age');
     * // => { 'fred': 40, 'pebbles': 1 } (iteration order is not guaranteed)
     */
    function mapValues(object, iteratee) {
      var result = {};
      iteratee = getIteratee(iteratee, 3);

      baseForOwn(object, function(value, key, object) {
        baseAssignValue(result, key, iteratee(value, key, object));
      });
      return result;
    }

    /**
     * This method is like `_.assign` except that it recursively merges own and
     * inherited enumerable string keyed properties of source objects into the
     * destination object. Source properties that resolve to `undefined` are
     * skipped if a destination value exists. Array and plain object properties
     * are merged recursively. Other objects and value types are overridden by
     * assignment. Source objects are applied from left to right. Subsequent
     * sources overwrite property assignments of previous sources.
     *
     * **Note:** This method mutates `object`.
     *
     * @static
     * @memberOf _
     * @since 0.5.0
     * @category Object
     * @param {Object} object The destination object.
     * @param {...Object} [sources] The source objects.
     * @returns {Object} Returns `object`.
     * @example
     *
     * var object = {
     *   'a': [{ 'b': 2 }, { 'd': 4 }]
     * };
     *
     * var other = {
     *   'a': [{ 'c': 3 }, { 'e': 5 }]
     * };
     *
     * _.merge(object, other);
     * // => { 'a': [{ 'b': 2, 'c': 3 }, { 'd': 4, 'e': 5 }] }
     */
    var merge = createAssigner(function(object, source, srcIndex) {
      baseMerge(object, source, srcIndex);
    });

    /**
     * This method is like `_.merge` except that it accepts `customizer` which
     * is invoked to produce the merged values of the destination and source
     * properties. If `customizer` returns `undefined`, merging is handled by the
     * method instead. The `customizer` is invoked with six arguments:
     * (objValue, srcValue, key, object, source, stack).
     *
     * **Note:** This method mutates `object`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Object
     * @param {Object} object The destination object.
     * @param {...Object} sources The source objects.
     * @param {Function} customizer The function to customize assigned values.
     * @returns {Object} Returns `object`.
     * @example
     *
     * function customizer(objValue, srcValue) {
     *   if (_.isArray(objValue)) {
     *     return objValue.concat(srcValue);
     *   }
     * }
     *
     * var object = { 'a': [1], 'b': [2] };
     * var other = { 'a': [3], 'b': [4] };
     *
     * _.mergeWith(object, other, customizer);
     * // => { 'a': [1, 3], 'b': [2, 4] }
     */
    var mergeWith = createAssigner(function(object, source, srcIndex, customizer) {
      baseMerge(object, source, srcIndex, customizer);
    });

    /**
     * The opposite of `_.pick`; this method creates an object composed of the
     * own and inherited enumerable property paths of `object` that are not omitted.
     *
     * **Note:** This method is considerably slower than `_.pick`.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Object
     * @param {Object} object The source object.
     * @param {...(string|string[])} [paths] The property paths to omit.
     * @returns {Object} Returns the new object.
     * @example
     *
     * var object = { 'a': 1, 'b': '2', 'c': 3 };
     *
     * _.omit(object, ['a', 'c']);
     * // => { 'b': '2' }
     */
    var omit = flatRest(function(object, paths) {
      var result = {};
      if (object == null) {
        return result;
      }
      var isDeep = false;
      paths = arrayMap(paths, function(path) {
        path = castPath(path, object);
        isDeep || (isDeep = path.length > 1);
        return path;
      });
      copyObject(object, getAllKeysIn(object), result);
      if (isDeep) {
        result = baseClone(result, CLONE_DEEP_FLAG | CLONE_FLAT_FLAG | CLONE_SYMBOLS_FLAG, customOmitClone);
      }
      var length = paths.length;
      while (length--) {
        baseUnset(result, paths[length]);
      }
      return result;
    });

    /**
     * The opposite of `_.pickBy`; this method creates an object composed of
     * the own and inherited enumerable string keyed properties of `object` that
     * `predicate` doesn't return truthy for. The predicate is invoked with two
     * arguments: (value, key).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Object
     * @param {Object} object The source object.
     * @param {Function} [predicate=_.identity] The function invoked per property.
     * @returns {Object} Returns the new object.
     * @example
     *
     * var object = { 'a': 1, 'b': '2', 'c': 3 };
     *
     * _.omitBy(object, _.isNumber);
     * // => { 'b': '2' }
     */
    function omitBy(object, predicate) {
      return pickBy(object, negate(getIteratee(predicate)));
    }

    /**
     * Creates an object composed of the picked `object` properties.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Object
     * @param {Object} object The source object.
     * @param {...(string|string[])} [paths] The property paths to pick.
     * @returns {Object} Returns the new object.
     * @example
     *
     * var object = { 'a': 1, 'b': '2', 'c': 3 };
     *
     * _.pick(object, ['a', 'c']);
     * // => { 'a': 1, 'c': 3 }
     */
    var pick = flatRest(function(object, paths) {
      return object == null ? {} : basePick(object, paths);
    });

    /**
     * Creates an object composed of the `object` properties `predicate` returns
     * truthy for. The predicate is invoked with two arguments: (value, key).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Object
     * @param {Object} object The source object.
     * @param {Function} [predicate=_.identity] The function invoked per property.
     * @returns {Object} Returns the new object.
     * @example
     *
     * var object = { 'a': 1, 'b': '2', 'c': 3 };
     *
     * _.pickBy(object, _.isNumber);
     * // => { 'a': 1, 'c': 3 }
     */
    function pickBy(object, predicate) {
      if (object == null) {
        return {};
      }
      var props = arrayMap(getAllKeysIn(object), function(prop) {
        return [prop];
      });
      predicate = getIteratee(predicate);
      return basePickBy(object, props, function(value, path) {
        return predicate(value, path[0]);
      });
    }

    /**
     * This method is like `_.get` except that if the resolved value is a
     * function it's invoked with the `this` binding of its parent object and
     * its result is returned.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Object
     * @param {Object} object The object to query.
     * @param {Array|string} path The path of the property to resolve.
     * @param {*} [defaultValue] The value returned for `undefined` resolved values.
     * @returns {*} Returns the resolved value.
     * @example
     *
     * var object = { 'a': [{ 'b': { 'c1': 3, 'c2': _.constant(4) } }] };
     *
     * _.result(object, 'a[0].b.c1');
     * // => 3
     *
     * _.result(object, 'a[0].b.c2');
     * // => 4
     *
     * _.result(object, 'a[0].b.c3', 'default');
     * // => 'default'
     *
     * _.result(object, 'a[0].b.c3', _.constant('default'));
     * // => 'default'
     */
    function result(object, path, defaultValue) {
      path = castPath(path, object);

      var index = -1,
          length = path.length;

      // Ensure the loop is entered when path is empty.
      if (!length) {
        length = 1;
        object = undefined;
      }
      while (++index < length) {
        var value = object == null ? undefined : object[toKey(path[index])];
        if (value === undefined) {
          index = length;
          value = defaultValue;
        }
        object = isFunction(value) ? value.call(object) : value;
      }
      return object;
    }

    /**
     * Sets the value at `path` of `object`. If a portion of `path` doesn't exist,
     * it's created. Arrays are created for missing index properties while objects
     * are created for all other missing properties. Use `_.setWith` to customize
     * `path` creation.
     *
     * **Note:** This method mutates `object`.
     *
     * @static
     * @memberOf _
     * @since 3.7.0
     * @category Object
     * @param {Object} object The object to modify.
     * @param {Array|string} path The path of the property to set.
     * @param {*} value The value to set.
     * @returns {Object} Returns `object`.
     * @example
     *
     * var object = { 'a': [{ 'b': { 'c': 3 } }] };
     *
     * _.set(object, 'a[0].b.c', 4);
     * console.log(object.a[0].b.c);
     * // => 4
     *
     * _.set(object, ['x', '0', 'y', 'z'], 5);
     * console.log(object.x[0].y.z);
     * // => 5
     */
    function set(object, path, value) {
      return object == null ? object : baseSet(object, path, value);
    }

    /**
     * This method is like `_.set` except that it accepts `customizer` which is
     * invoked to produce the objects of `path`.  If `customizer` returns `undefined`
     * path creation is handled by the method instead. The `customizer` is invoked
     * with three arguments: (nsValue, key, nsObject).
     *
     * **Note:** This method mutates `object`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Object
     * @param {Object} object The object to modify.
     * @param {Array|string} path The path of the property to set.
     * @param {*} value The value to set.
     * @param {Function} [customizer] The function to customize assigned values.
     * @returns {Object} Returns `object`.
     * @example
     *
     * var object = {};
     *
     * _.setWith(object, '[0][1]', 'a', Object);
     * // => { '0': { '1': 'a' } }
     */
    function setWith(object, path, value, customizer) {
      customizer = typeof customizer == 'function' ? customizer : undefined;
      return object == null ? object : baseSet(object, path, value, customizer);
    }

    /**
     * Creates an array of own enumerable string keyed-value pairs for `object`
     * which can be consumed by `_.fromPairs`. If `object` is a map or set, its
     * entries are returned.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @alias entries
     * @category Object
     * @param {Object} object The object to query.
     * @returns {Array} Returns the key-value pairs.
     * @example
     *
     * function Foo() {
     *   this.a = 1;
     *   this.b = 2;
     * }
     *
     * Foo.prototype.c = 3;
     *
     * _.toPairs(new Foo);
     * // => [['a', 1], ['b', 2]] (iteration order is not guaranteed)
     */
    var toPairs = createToPairs(keys);

    /**
     * Creates an array of own and inherited enumerable string keyed-value pairs
     * for `object` which can be consumed by `_.fromPairs`. If `object` is a map
     * or set, its entries are returned.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @alias entriesIn
     * @category Object
     * @param {Object} object The object to query.
     * @returns {Array} Returns the key-value pairs.
     * @example
     *
     * function Foo() {
     *   this.a = 1;
     *   this.b = 2;
     * }
     *
     * Foo.prototype.c = 3;
     *
     * _.toPairsIn(new Foo);
     * // => [['a', 1], ['b', 2], ['c', 3]] (iteration order is not guaranteed)
     */
    var toPairsIn = createToPairs(keysIn);

    /**
     * An alternative to `_.reduce`; this method transforms `object` to a new
     * `accumulator` object which is the result of running each of its own
     * enumerable string keyed properties thru `iteratee`, with each invocation
     * potentially mutating the `accumulator` object. If `accumulator` is not
     * provided, a new object with the same `[[Prototype]]` will be used. The
     * iteratee is invoked with four arguments: (accumulator, value, key, object).
     * Iteratee functions may exit iteration early by explicitly returning `false`.
     *
     * @static
     * @memberOf _
     * @since 1.3.0
     * @category Object
     * @param {Object} object The object to iterate over.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @param {*} [accumulator] The custom accumulator value.
     * @returns {*} Returns the accumulated value.
     * @example
     *
     * _.transform([2, 3, 4], function(result, n) {
     *   result.push(n *= n);
     *   return n % 2 == 0;
     * }, []);
     * // => [4, 9]
     *
     * _.transform({ 'a': 1, 'b': 2, 'c': 1 }, function(result, value, key) {
     *   (result[value] || (result[value] = [])).push(key);
     * }, {});
     * // => { '1': ['a', 'c'], '2': ['b'] }
     */
    function transform(object, iteratee, accumulator) {
      var isArr = isArray(object),
          isArrLike = isArr || isBuffer(object) || isTypedArray(object);

      iteratee = getIteratee(iteratee, 4);
      if (accumulator == null) {
        var Ctor = object && object.constructor;
        if (isArrLike) {
          accumulator = isArr ? new Ctor : [];
        }
        else if (isObject(object)) {
          accumulator = isFunction(Ctor) ? baseCreate(getPrototype(object)) : {};
        }
        else {
          accumulator = {};
        }
      }
      (isArrLike ? arrayEach : baseForOwn)(object, function(value, index, object) {
        return iteratee(accumulator, value, index, object);
      });
      return accumulator;
    }

    /**
     * Removes the property at `path` of `object`.
     *
     * **Note:** This method mutates `object`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Object
     * @param {Object} object The object to modify.
     * @param {Array|string} path The path of the property to unset.
     * @returns {boolean} Returns `true` if the property is deleted, else `false`.
     * @example
     *
     * var object = { 'a': [{ 'b': { 'c': 7 } }] };
     * _.unset(object, 'a[0].b.c');
     * // => true
     *
     * console.log(object);
     * // => { 'a': [{ 'b': {} }] };
     *
     * _.unset(object, ['a', '0', 'b', 'c']);
     * // => true
     *
     * console.log(object);
     * // => { 'a': [{ 'b': {} }] };
     */
    function unset(object, path) {
      return object == null ? true : baseUnset(object, path);
    }

    /**
     * This method is like `_.set` except that accepts `updater` to produce the
     * value to set. Use `_.updateWith` to customize `path` creation. The `updater`
     * is invoked with one argument: (value).
     *
     * **Note:** This method mutates `object`.
     *
     * @static
     * @memberOf _
     * @since 4.6.0
     * @category Object
     * @param {Object} object The object to modify.
     * @param {Array|string} path The path of the property to set.
     * @param {Function} updater The function to produce the updated value.
     * @returns {Object} Returns `object`.
     * @example
     *
     * var object = { 'a': [{ 'b': { 'c': 3 } }] };
     *
     * _.update(object, 'a[0].b.c', function(n) { return n * n; });
     * console.log(object.a[0].b.c);
     * // => 9
     *
     * _.update(object, 'x[0].y.z', function(n) { return n ? n + 1 : 0; });
     * console.log(object.x[0].y.z);
     * // => 0
     */
    function update(object, path, updater) {
      return object == null ? object : baseUpdate(object, path, castFunction(updater));
    }

    /**
     * This method is like `_.update` except that it accepts `customizer` which is
     * invoked to produce the objects of `path`.  If `customizer` returns `undefined`
     * path creation is handled by the method instead. The `customizer` is invoked
     * with three arguments: (nsValue, key, nsObject).
     *
     * **Note:** This method mutates `object`.
     *
     * @static
     * @memberOf _
     * @since 4.6.0
     * @category Object
     * @param {Object} object The object to modify.
     * @param {Array|string} path The path of the property to set.
     * @param {Function} updater The function to produce the updated value.
     * @param {Function} [customizer] The function to customize assigned values.
     * @returns {Object} Returns `object`.
     * @example
     *
     * var object = {};
     *
     * _.updateWith(object, '[0][1]', _.constant('a'), Object);
     * // => { '0': { '1': 'a' } }
     */
    function updateWith(object, path, updater, customizer) {
      customizer = typeof customizer == 'function' ? customizer : undefined;
      return object == null ? object : baseUpdate(object, path, castFunction(updater), customizer);
    }

    /**
     * Creates an array of the own enumerable string keyed property values of `object`.
     *
     * **Note:** Non-object values are coerced to objects.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Object
     * @param {Object} object The object to query.
     * @returns {Array} Returns the array of property values.
     * @example
     *
     * function Foo() {
     *   this.a = 1;
     *   this.b = 2;
     * }
     *
     * Foo.prototype.c = 3;
     *
     * _.values(new Foo);
     * // => [1, 2] (iteration order is not guaranteed)
     *
     * _.values('hi');
     * // => ['h', 'i']
     */
    function values(object) {
      return object == null ? [] : baseValues(object, keys(object));
    }

    /**
     * Creates an array of the own and inherited enumerable string keyed property
     * values of `object`.
     *
     * **Note:** Non-object values are coerced to objects.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Object
     * @param {Object} object The object to query.
     * @returns {Array} Returns the array of property values.
     * @example
     *
     * function Foo() {
     *   this.a = 1;
     *   this.b = 2;
     * }
     *
     * Foo.prototype.c = 3;
     *
     * _.valuesIn(new Foo);
     * // => [1, 2, 3] (iteration order is not guaranteed)
     */
    function valuesIn(object) {
      return object == null ? [] : baseValues(object, keysIn(object));
    }

    /*------------------------------------------------------------------------*/

    /**
     * Clamps `number` within the inclusive `lower` and `upper` bounds.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Number
     * @param {number} number The number to clamp.
     * @param {number} [lower] The lower bound.
     * @param {number} upper The upper bound.
     * @returns {number} Returns the clamped number.
     * @example
     *
     * _.clamp(-10, -5, 5);
     * // => -5
     *
     * _.clamp(10, -5, 5);
     * // => 5
     */
    function clamp(number, lower, upper) {
      if (upper === undefined) {
        upper = lower;
        lower = undefined;
      }
      if (upper !== undefined) {
        upper = toNumber(upper);
        upper = upper === upper ? upper : 0;
      }
      if (lower !== undefined) {
        lower = toNumber(lower);
        lower = lower === lower ? lower : 0;
      }
      return baseClamp(toNumber(number), lower, upper);
    }

    /**
     * Checks if `n` is between `start` and up to, but not including, `end`. If
     * `end` is not specified, it's set to `start` with `start` then set to `0`.
     * If `start` is greater than `end` the params are swapped to support
     * negative ranges.
     *
     * @static
     * @memberOf _
     * @since 3.3.0
     * @category Number
     * @param {number} number The number to check.
     * @param {number} [start=0] The start of the range.
     * @param {number} end The end of the range.
     * @returns {boolean} Returns `true` if `number` is in the range, else `false`.
     * @see _.range, _.rangeRight
     * @example
     *
     * _.inRange(3, 2, 4);
     * // => true
     *
     * _.inRange(4, 8);
     * // => true
     *
     * _.inRange(4, 2);
     * // => false
     *
     * _.inRange(2, 2);
     * // => false
     *
     * _.inRange(1.2, 2);
     * // => true
     *
     * _.inRange(5.2, 4);
     * // => false
     *
     * _.inRange(-3, -2, -6);
     * // => true
     */
    function inRange(number, start, end) {
      start = toFinite(start);
      if (end === undefined) {
        end = start;
        start = 0;
      } else {
        end = toFinite(end);
      }
      number = toNumber(number);
      return baseInRange(number, start, end);
    }

    /**
     * Produces a random number between the inclusive `lower` and `upper` bounds.
     * If only one argument is provided a number between `0` and the given number
     * is returned. If `floating` is `true`, or either `lower` or `upper` are
     * floats, a floating-point number is returned instead of an integer.
     *
     * **Note:** JavaScript follows the IEEE-754 standard for resolving
     * floating-point values which can produce unexpected results.
     *
     * @static
     * @memberOf _
     * @since 0.7.0
     * @category Number
     * @param {number} [lower=0] The lower bound.
     * @param {number} [upper=1] The upper bound.
     * @param {boolean} [floating] Specify returning a floating-point number.
     * @returns {number} Returns the random number.
     * @example
     *
     * _.random(0, 5);
     * // => an integer between 0 and 5
     *
     * _.random(5);
     * // => also an integer between 0 and 5
     *
     * _.random(5, true);
     * // => a floating-point number between 0 and 5
     *
     * _.random(1.2, 5.2);
     * // => a floating-point number between 1.2 and 5.2
     */
    function random(lower, upper, floating) {
      if (floating && typeof floating != 'boolean' && isIterateeCall(lower, upper, floating)) {
        upper = floating = undefined;
      }
      if (floating === undefined) {
        if (typeof upper == 'boolean') {
          floating = upper;
          upper = undefined;
        }
        else if (typeof lower == 'boolean') {
          floating = lower;
          lower = undefined;
        }
      }
      if (lower === undefined && upper === undefined) {
        lower = 0;
        upper = 1;
      }
      else {
        lower = toFinite(lower);
        if (upper === undefined) {
          upper = lower;
          lower = 0;
        } else {
          upper = toFinite(upper);
        }
      }
      if (lower > upper) {
        var temp = lower;
        lower = upper;
        upper = temp;
      }
      if (floating || lower % 1 || upper % 1) {
        var rand = nativeRandom();
        return nativeMin(lower + (rand * (upper - lower + freeParseFloat('1e-' + ((rand + '').length - 1)))), upper);
      }
      return baseRandom(lower, upper);
    }

    /*------------------------------------------------------------------------*/

    /**
     * Converts `string` to [camel case](https://en.wikipedia.org/wiki/CamelCase).
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category String
     * @param {string} [string=''] The string to convert.
     * @returns {string} Returns the camel cased string.
     * @example
     *
     * _.camelCase('Foo Bar');
     * // => 'fooBar'
     *
     * _.camelCase('--foo-bar--');
     * // => 'fooBar'
     *
     * _.camelCase('__FOO_BAR__');
     * // => 'fooBar'
     */
    var camelCase = createCompounder(function(result, word, index) {
      word = word.toLowerCase();
      return result + (index ? capitalize(word) : word);
    });

    /**
     * Converts the first character of `string` to upper case and the remaining
     * to lower case.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category String
     * @param {string} [string=''] The string to capitalize.
     * @returns {string} Returns the capitalized string.
     * @example
     *
     * _.capitalize('FRED');
     * // => 'Fred'
     */
    function capitalize(string) {
      return upperFirst(toString(string).toLowerCase());
    }

    /**
     * Deburrs `string` by converting
     * [Latin-1 Supplement](https://en.wikipedia.org/wiki/Latin-1_Supplement_(Unicode_block)#Character_table)
     * and [Latin Extended-A](https://en.wikipedia.org/wiki/Latin_Extended-A)
     * letters to basic Latin letters and removing
     * [combining diacritical marks](https://en.wikipedia.org/wiki/Combining_Diacritical_Marks).
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category String
     * @param {string} [string=''] The string to deburr.
     * @returns {string} Returns the deburred string.
     * @example
     *
     * _.deburr('déjà vu');
     * // => 'deja vu'
     */
    function deburr(string) {
      string = toString(string);
      return string && string.replace(reLatin, deburrLetter).replace(reComboMark, '');
    }

    /**
     * Checks if `string` ends with the given target string.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category String
     * @param {string} [string=''] The string to inspect.
     * @param {string} [target] The string to search for.
     * @param {number} [position=string.length] The position to search up to.
     * @returns {boolean} Returns `true` if `string` ends with `target`,
     *  else `false`.
     * @example
     *
     * _.endsWith('abc', 'c');
     * // => true
     *
     * _.endsWith('abc', 'b');
     * // => false
     *
     * _.endsWith('abc', 'b', 2);
     * // => true
     */
    function endsWith(string, target, position) {
      string = toString(string);
      target = baseToString(target);

      var length = string.length;
      position = position === undefined
        ? length
        : baseClamp(toInteger(position), 0, length);

      var end = position;
      position -= target.length;
      return position >= 0 && string.slice(position, end) == target;
    }

    /**
     * Converts the characters "&", "<", ">", '"', and "'" in `string` to their
     * corresponding HTML entities.
     *
     * **Note:** No other characters are escaped. To escape additional
     * characters use a third-party library like [_he_](https://mths.be/he).
     *
     * Though the ">" character is escaped for symmetry, characters like
     * ">" and "/" don't need escaping in HTML and have no special meaning
     * unless they're part of a tag or unquoted attribute value. See
     * [Mathias Bynens's article](https://mathiasbynens.be/notes/ambiguous-ampersands)
     * (under "semi-related fun fact") for more details.
     *
     * When working with HTML you should always
     * [quote attribute values](http://wonko.com/post/html-escaping) to reduce
     * XSS vectors.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category String
     * @param {string} [string=''] The string to escape.
     * @returns {string} Returns the escaped string.
     * @example
     *
     * _.escape('fred, barney, & pebbles');
     * // => 'fred, barney, &amp; pebbles'
     */
    function escape(string) {
      string = toString(string);
      return (string && reHasUnescapedHtml.test(string))
        ? string.replace(reUnescapedHtml, escapeHtmlChar)
        : string;
    }

    /**
     * Escapes the `RegExp` special characters "^", "$", "\", ".", "*", "+",
     * "?", "(", ")", "[", "]", "{", "}", and "|" in `string`.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category String
     * @param {string} [string=''] The string to escape.
     * @returns {string} Returns the escaped string.
     * @example
     *
     * _.escapeRegExp('[lodash](https://lodash.com/)');
     * // => '\[lodash\]\(https://lodash\.com/\)'
     */
    function escapeRegExp(string) {
      string = toString(string);
      return (string && reHasRegExpChar.test(string))
        ? string.replace(reRegExpChar, '\\$&')
        : string;
    }

    /**
     * Converts `string` to
     * [kebab case](https://en.wikipedia.org/wiki/Letter_case#Special_case_styles).
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category String
     * @param {string} [string=''] The string to convert.
     * @returns {string} Returns the kebab cased string.
     * @example
     *
     * _.kebabCase('Foo Bar');
     * // => 'foo-bar'
     *
     * _.kebabCase('fooBar');
     * // => 'foo-bar'
     *
     * _.kebabCase('__FOO_BAR__');
     * // => 'foo-bar'
     */
    var kebabCase = createCompounder(function(result, word, index) {
      return result + (index ? '-' : '') + word.toLowerCase();
    });

    /**
     * Converts `string`, as space separated words, to lower case.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category String
     * @param {string} [string=''] The string to convert.
     * @returns {string} Returns the lower cased string.
     * @example
     *
     * _.lowerCase('--Foo-Bar--');
     * // => 'foo bar'
     *
     * _.lowerCase('fooBar');
     * // => 'foo bar'
     *
     * _.lowerCase('__FOO_BAR__');
     * // => 'foo bar'
     */
    var lowerCase = createCompounder(function(result, word, index) {
      return result + (index ? ' ' : '') + word.toLowerCase();
    });

    /**
     * Converts the first character of `string` to lower case.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category String
     * @param {string} [string=''] The string to convert.
     * @returns {string} Returns the converted string.
     * @example
     *
     * _.lowerFirst('Fred');
     * // => 'fred'
     *
     * _.lowerFirst('FRED');
     * // => 'fRED'
     */
    var lowerFirst = createCaseFirst('toLowerCase');

    /**
     * Pads `string` on the left and right sides if it's shorter than `length`.
     * Padding characters are truncated if they can't be evenly divided by `length`.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category String
     * @param {string} [string=''] The string to pad.
     * @param {number} [length=0] The padding length.
     * @param {string} [chars=' '] The string used as padding.
     * @returns {string} Returns the padded string.
     * @example
     *
     * _.pad('abc', 8);
     * // => '  abc   '
     *
     * _.pad('abc', 8, '_-');
     * // => '_-abc_-_'
     *
     * _.pad('abc', 3);
     * // => 'abc'
     */
    function pad(string, length, chars) {
      string = toString(string);
      length = toInteger(length);

      var strLength = length ? stringSize(string) : 0;
      if (!length || strLength >= length) {
        return string;
      }
      var mid = (length - strLength) / 2;
      return (
        createPadding(nativeFloor(mid), chars) +
        string +
        createPadding(nativeCeil(mid), chars)
      );
    }

    /**
     * Pads `string` on the right side if it's shorter than `length`. Padding
     * characters are truncated if they exceed `length`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category String
     * @param {string} [string=''] The string to pad.
     * @param {number} [length=0] The padding length.
     * @param {string} [chars=' '] The string used as padding.
     * @returns {string} Returns the padded string.
     * @example
     *
     * _.padEnd('abc', 6);
     * // => 'abc   '
     *
     * _.padEnd('abc', 6, '_-');
     * // => 'abc_-_'
     *
     * _.padEnd('abc', 3);
     * // => 'abc'
     */
    function padEnd(string, length, chars) {
      string = toString(string);
      length = toInteger(length);

      var strLength = length ? stringSize(string) : 0;
      return (length && strLength < length)
        ? (string + createPadding(length - strLength, chars))
        : string;
    }

    /**
     * Pads `string` on the left side if it's shorter than `length`. Padding
     * characters are truncated if they exceed `length`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category String
     * @param {string} [string=''] The string to pad.
     * @param {number} [length=0] The padding length.
     * @param {string} [chars=' '] The string used as padding.
     * @returns {string} Returns the padded string.
     * @example
     *
     * _.padStart('abc', 6);
     * // => '   abc'
     *
     * _.padStart('abc', 6, '_-');
     * // => '_-_abc'
     *
     * _.padStart('abc', 3);
     * // => 'abc'
     */
    function padStart(string, length, chars) {
      string = toString(string);
      length = toInteger(length);

      var strLength = length ? stringSize(string) : 0;
      return (length && strLength < length)
        ? (createPadding(length - strLength, chars) + string)
        : string;
    }

    /**
     * Converts `string` to an integer of the specified radix. If `radix` is
     * `undefined` or `0`, a `radix` of `10` is used unless `value` is a
     * hexadecimal, in which case a `radix` of `16` is used.
     *
     * **Note:** This method aligns with the
     * [ES5 implementation](https://es5.github.io/#x15.1.2.2) of `parseInt`.
     *
     * @static
     * @memberOf _
     * @since 1.1.0
     * @category String
     * @param {string} string The string to convert.
     * @param {number} [radix=10] The radix to interpret `value` by.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {number} Returns the converted integer.
     * @example
     *
     * _.parseInt('08');
     * // => 8
     *
     * _.map(['6', '08', '10'], _.parseInt);
     * // => [6, 8, 10]
     */
    function parseInt(string, radix, guard) {
      if (guard || radix == null) {
        radix = 0;
      } else if (radix) {
        radix = +radix;
      }
      return nativeParseInt(toString(string).replace(reTrimStart, ''), radix || 0);
    }

    /**
     * Repeats the given string `n` times.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category String
     * @param {string} [string=''] The string to repeat.
     * @param {number} [n=1] The number of times to repeat the string.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {string} Returns the repeated string.
     * @example
     *
     * _.repeat('*', 3);
     * // => '***'
     *
     * _.repeat('abc', 2);
     * // => 'abcabc'
     *
     * _.repeat('abc', 0);
     * // => ''
     */
    function repeat(string, n, guard) {
      if ((guard ? isIterateeCall(string, n, guard) : n === undefined)) {
        n = 1;
      } else {
        n = toInteger(n);
      }
      return baseRepeat(toString(string), n);
    }

    /**
     * Replaces matches for `pattern` in `string` with `replacement`.
     *
     * **Note:** This method is based on
     * [`String#replace`](https://mdn.io/String/replace).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category String
     * @param {string} [string=''] The string to modify.
     * @param {RegExp|string} pattern The pattern to replace.
     * @param {Function|string} replacement The match replacement.
     * @returns {string} Returns the modified string.
     * @example
     *
     * _.replace('Hi Fred', 'Fred', 'Barney');
     * // => 'Hi Barney'
     */
    function replace() {
      var args = arguments,
          string = toString(args[0]);

      return args.length < 3 ? string : string.replace(args[1], args[2]);
    }

    /**
     * Converts `string` to
     * [snake case](https://en.wikipedia.org/wiki/Snake_case).
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category String
     * @param {string} [string=''] The string to convert.
     * @returns {string} Returns the snake cased string.
     * @example
     *
     * _.snakeCase('Foo Bar');
     * // => 'foo_bar'
     *
     * _.snakeCase('fooBar');
     * // => 'foo_bar'
     *
     * _.snakeCase('--FOO-BAR--');
     * // => 'foo_bar'
     */
    var snakeCase = createCompounder(function(result, word, index) {
      return result + (index ? '_' : '') + word.toLowerCase();
    });

    /**
     * Splits `string` by `separator`.
     *
     * **Note:** This method is based on
     * [`String#split`](https://mdn.io/String/split).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category String
     * @param {string} [string=''] The string to split.
     * @param {RegExp|string} separator The separator pattern to split by.
     * @param {number} [limit] The length to truncate results to.
     * @returns {Array} Returns the string segments.
     * @example
     *
     * _.split('a-b-c', '-', 2);
     * // => ['a', 'b']
     */
    function split(string, separator, limit) {
      if (limit && typeof limit != 'number' && isIterateeCall(string, separator, limit)) {
        separator = limit = undefined;
      }
      limit = limit === undefined ? MAX_ARRAY_LENGTH : limit >>> 0;
      if (!limit) {
        return [];
      }
      string = toString(string);
      if (string && (
            typeof separator == 'string' ||
            (separator != null && !isRegExp(separator))
          )) {
        separator = baseToString(separator);
        if (!separator && hasUnicode(string)) {
          return castSlice(stringToArray(string), 0, limit);
        }
      }
      return string.split(separator, limit);
    }

    /**
     * Converts `string` to
     * [start case](https://en.wikipedia.org/wiki/Letter_case#Stylistic_or_specialised_usage).
     *
     * @static
     * @memberOf _
     * @since 3.1.0
     * @category String
     * @param {string} [string=''] The string to convert.
     * @returns {string} Returns the start cased string.
     * @example
     *
     * _.startCase('--foo-bar--');
     * // => 'Foo Bar'
     *
     * _.startCase('fooBar');
     * // => 'Foo Bar'
     *
     * _.startCase('__FOO_BAR__');
     * // => 'FOO BAR'
     */
    var startCase = createCompounder(function(result, word, index) {
      return result + (index ? ' ' : '') + upperFirst(word);
    });

    /**
     * Checks if `string` starts with the given target string.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category String
     * @param {string} [string=''] The string to inspect.
     * @param {string} [target] The string to search for.
     * @param {number} [position=0] The position to search from.
     * @returns {boolean} Returns `true` if `string` starts with `target`,
     *  else `false`.
     * @example
     *
     * _.startsWith('abc', 'a');
     * // => true
     *
     * _.startsWith('abc', 'b');
     * // => false
     *
     * _.startsWith('abc', 'b', 1);
     * // => true
     */
    function startsWith(string, target, position) {
      string = toString(string);
      position = position == null
        ? 0
        : baseClamp(toInteger(position), 0, string.length);

      target = baseToString(target);
      return string.slice(position, position + target.length) == target;
    }

    /**
     * Creates a compiled template function that can interpolate data properties
     * in "interpolate" delimiters, HTML-escape interpolated data properties in
     * "escape" delimiters, and execute JavaScript in "evaluate" delimiters. Data
     * properties may be accessed as free variables in the template. If a setting
     * object is given, it takes precedence over `_.templateSettings` values.
     *
     * **Note:** In the development build `_.template` utilizes
     * [sourceURLs](http://www.html5rocks.com/en/tutorials/developertools/sourcemaps/#toc-sourceurl)
     * for easier debugging.
     *
     * For more information on precompiling templates see
     * [lodash's custom builds documentation](https://lodash.com/custom-builds).
     *
     * For more information on Chrome extension sandboxes see
     * [Chrome's extensions documentation](https://developer.chrome.com/extensions/sandboxingEval).
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category String
     * @param {string} [string=''] The template string.
     * @param {Object} [options={}] The options object.
     * @param {RegExp} [options.escape=_.templateSettings.escape]
     *  The HTML "escape" delimiter.
     * @param {RegExp} [options.evaluate=_.templateSettings.evaluate]
     *  The "evaluate" delimiter.
     * @param {Object} [options.imports=_.templateSettings.imports]
     *  An object to import into the template as free variables.
     * @param {RegExp} [options.interpolate=_.templateSettings.interpolate]
     *  The "interpolate" delimiter.
     * @param {string} [options.sourceURL='lodash.templateSources[n]']
     *  The sourceURL of the compiled template.
     * @param {string} [options.variable='obj']
     *  The data object variable name.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {Function} Returns the compiled template function.
     * @example
     *
     * // Use the "interpolate" delimiter to create a compiled template.
     * var compiled = _.template('hello <%= user %>!');
     * compiled({ 'user': 'fred' });
     * // => 'hello fred!'
     *
     * // Use the HTML "escape" delimiter to escape data property values.
     * var compiled = _.template('<b><%- value %></b>');
     * compiled({ 'value': '<script>' });
     * // => '<b>&lt;script&gt;</b>'
     *
     * // Use the "evaluate" delimiter to execute JavaScript and generate HTML.
     * var compiled = _.template('<% _.forEach(users, function(user) { %><li><%- user %></li><% }); %>');
     * compiled({ 'users': ['fred', 'barney'] });
     * // => '<li>fred</li><li>barney</li>'
     *
     * // Use the internal `print` function in "evaluate" delimiters.
     * var compiled = _.template('<% print("hello " + user); %>!');
     * compiled({ 'user': 'barney' });
     * // => 'hello barney!'
     *
     * // Use the ES template literal delimiter as an "interpolate" delimiter.
     * // Disable support by replacing the "interpolate" delimiter.
     * var compiled = _.template('hello ${ user }!');
     * compiled({ 'user': 'pebbles' });
     * // => 'hello pebbles!'
     *
     * // Use backslashes to treat delimiters as plain text.
     * var compiled = _.template('<%= "\\<%- value %\\>" %>');
     * compiled({ 'value': 'ignored' });
     * // => '<%- value %>'
     *
     * // Use the `imports` option to import `jQuery` as `jq`.
     * var text = '<% jq.each(users, function(user) { %><li><%- user %></li><% }); %>';
     * var compiled = _.template(text, { 'imports': { 'jq': jQuery } });
     * compiled({ 'users': ['fred', 'barney'] });
     * // => '<li>fred</li><li>barney</li>'
     *
     * // Use the `sourceURL` option to specify a custom sourceURL for the template.
     * var compiled = _.template('hello <%= user %>!', { 'sourceURL': '/basic/greeting.jst' });
     * compiled(data);
     * // => Find the source of "greeting.jst" under the Sources tab or Resources panel of the web inspector.
     *
     * // Use the `variable` option to ensure a with-statement isn't used in the compiled template.
     * var compiled = _.template('hi <%= data.user %>!', { 'variable': 'data' });
     * compiled.source;
     * // => function(data) {
     * //   var __t, __p = '';
     * //   __p += 'hi ' + ((__t = ( data.user )) == null ? '' : __t) + '!';
     * //   return __p;
     * // }
     *
     * // Use custom template delimiters.
     * _.templateSettings.interpolate = /{{([\s\S]+?)}}/g;
     * var compiled = _.template('hello {{ user }}!');
     * compiled({ 'user': 'mustache' });
     * // => 'hello mustache!'
     *
     * // Use the `source` property to inline compiled templates for meaningful
     * // line numbers in error messages and stack traces.
     * fs.writeFileSync(path.join(process.cwd(), 'jst.js'), '\
     *   var JST = {\
     *     "main": ' + _.template(mainText).source + '\
     *   };\
     * ');
     */
    function template(string, options, guard) {
      // Based on John Resig's `tmpl` implementation
      // (http://ejohn.org/blog/javascript-micro-templating/)
      // and Laura Doktorova's doT.js (https://github.com/olado/doT).
      var settings = lodash.templateSettings;

      if (guard && isIterateeCall(string, options, guard)) {
        options = undefined;
      }
      string = toString(string);
      options = assignInWith({}, options, settings, customDefaultsAssignIn);

      var imports = assignInWith({}, options.imports, settings.imports, customDefaultsAssignIn),
          importsKeys = keys(imports),
          importsValues = baseValues(imports, importsKeys);

      var isEscaping,
          isEvaluating,
          index = 0,
          interpolate = options.interpolate || reNoMatch,
          source = "__p += '";

      // Compile the regexp to match each delimiter.
      var reDelimiters = RegExp(
        (options.escape || reNoMatch).source + '|' +
        interpolate.source + '|' +
        (interpolate === reInterpolate ? reEsTemplate : reNoMatch).source + '|' +
        (options.evaluate || reNoMatch).source + '|$'
      , 'g');

      // Use a sourceURL for easier debugging.
      // The sourceURL gets injected into the source that's eval-ed, so be careful
      // to normalize all kinds of whitespace, so e.g. newlines (and unicode versions of it) can't sneak in
      // and escape the comment, thus injecting code that gets evaled.
      var sourceURL = '//# sourceURL=' +
        (hasOwnProperty.call(options, 'sourceURL')
          ? (options.sourceURL + '').replace(/\s/g, ' ')
          : ('lodash.templateSources[' + (++templateCounter) + ']')
        ) + '\n';

      string.replace(reDelimiters, function(match, escapeValue, interpolateValue, esTemplateValue, evaluateValue, offset) {
        interpolateValue || (interpolateValue = esTemplateValue);

        // Escape characters that can't be included in string literals.
        source += string.slice(index, offset).replace(reUnescapedString, escapeStringChar);

        // Replace delimiters with snippets.
        if (escapeValue) {
          isEscaping = true;
          source += "' +\n__e(" + escapeValue + ") +\n'";
        }
        if (evaluateValue) {
          isEvaluating = true;
          source += "';\n" + evaluateValue + ";\n__p += '";
        }
        if (interpolateValue) {
          source += "' +\n((__t = (" + interpolateValue + ")) == null ? '' : __t) +\n'";
        }
        index = offset + match.length;

        // The JS engine embedded in Adobe products needs `match` returned in
        // order to produce the correct `offset` value.
        return match;
      });

      source += "';\n";

      // If `variable` is not specified wrap a with-statement around the generated
      // code to add the data object to the top of the scope chain.
      var variable = hasOwnProperty.call(options, 'variable') && options.variable;
      if (!variable) {
        source = 'with (obj) {\n' + source + '\n}\n';
      }
      // Throw an error if a forbidden character was found in `variable`, to prevent
      // potential command injection attacks.
      else if (reForbiddenIdentifierChars.test(variable)) {
        throw new Error(INVALID_TEMPL_VAR_ERROR_TEXT);
      }

      // Cleanup code by stripping empty strings.
      source = (isEvaluating ? source.replace(reEmptyStringLeading, '') : source)
        .replace(reEmptyStringMiddle, '$1')
        .replace(reEmptyStringTrailing, '$1;');

      // Frame code as the function body.
      source = 'function(' + (variable || 'obj') + ') {\n' +
        (variable
          ? ''
          : 'obj || (obj = {});\n'
        ) +
        "var __t, __p = ''" +
        (isEscaping
           ? ', __e = _.escape'
           : ''
        ) +
        (isEvaluating
          ? ', __j = Array.prototype.join;\n' +
            "function print() { __p += __j.call(arguments, '') }\n"
          : ';\n'
        ) +
        source +
        'return __p\n}';

      var result = attempt(function() {
        return Function(importsKeys, sourceURL + 'return ' + source)
          .apply(undefined, importsValues);
      });

      // Provide the compiled function's source by its `toString` method or
      // the `source` property as a convenience for inlining compiled templates.
      result.source = source;
      if (isError(result)) {
        throw result;
      }
      return result;
    }

    /**
     * Converts `string`, as a whole, to lower case just like
     * [String#toLowerCase](https://mdn.io/toLowerCase).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category String
     * @param {string} [string=''] The string to convert.
     * @returns {string} Returns the lower cased string.
     * @example
     *
     * _.toLower('--Foo-Bar--');
     * // => '--foo-bar--'
     *
     * _.toLower('fooBar');
     * // => 'foobar'
     *
     * _.toLower('__FOO_BAR__');
     * // => '__foo_bar__'
     */
    function toLower(value) {
      return toString(value).toLowerCase();
    }

    /**
     * Converts `string`, as a whole, to upper case just like
     * [String#toUpperCase](https://mdn.io/toUpperCase).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category String
     * @param {string} [string=''] The string to convert.
     * @returns {string} Returns the upper cased string.
     * @example
     *
     * _.toUpper('--foo-bar--');
     * // => '--FOO-BAR--'
     *
     * _.toUpper('fooBar');
     * // => 'FOOBAR'
     *
     * _.toUpper('__foo_bar__');
     * // => '__FOO_BAR__'
     */
    function toUpper(value) {
      return toString(value).toUpperCase();
    }

    /**
     * Removes leading and trailing whitespace or specified characters from `string`.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category String
     * @param {string} [string=''] The string to trim.
     * @param {string} [chars=whitespace] The characters to trim.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {string} Returns the trimmed string.
     * @example
     *
     * _.trim('  abc  ');
     * // => 'abc'
     *
     * _.trim('-_-abc-_-', '_-');
     * // => 'abc'
     *
     * _.map(['  foo  ', '  bar  '], _.trim);
     * // => ['foo', 'bar']
     */
    function trim(string, chars, guard) {
      string = toString(string);
      if (string && (guard || chars === undefined)) {
        return baseTrim(string);
      }
      if (!string || !(chars = baseToString(chars))) {
        return string;
      }
      var strSymbols = stringToArray(string),
          chrSymbols = stringToArray(chars),
          start = charsStartIndex(strSymbols, chrSymbols),
          end = charsEndIndex(strSymbols, chrSymbols) + 1;

      return castSlice(strSymbols, start, end).join('');
    }

    /**
     * Removes trailing whitespace or specified characters from `string`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category String
     * @param {string} [string=''] The string to trim.
     * @param {string} [chars=whitespace] The characters to trim.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {string} Returns the trimmed string.
     * @example
     *
     * _.trimEnd('  abc  ');
     * // => '  abc'
     *
     * _.trimEnd('-_-abc-_-', '_-');
     * // => '-_-abc'
     */
    function trimEnd(string, chars, guard) {
      string = toString(string);
      if (string && (guard || chars === undefined)) {
        return string.slice(0, trimmedEndIndex(string) + 1);
      }
      if (!string || !(chars = baseToString(chars))) {
        return string;
      }
      var strSymbols = stringToArray(string),
          end = charsEndIndex(strSymbols, stringToArray(chars)) + 1;

      return castSlice(strSymbols, 0, end).join('');
    }

    /**
     * Removes leading whitespace or specified characters from `string`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category String
     * @param {string} [string=''] The string to trim.
     * @param {string} [chars=whitespace] The characters to trim.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {string} Returns the trimmed string.
     * @example
     *
     * _.trimStart('  abc  ');
     * // => 'abc  '
     *
     * _.trimStart('-_-abc-_-', '_-');
     * // => 'abc-_-'
     */
    function trimStart(string, chars, guard) {
      string = toString(string);
      if (string && (guard || chars === undefined)) {
        return string.replace(reTrimStart, '');
      }
      if (!string || !(chars = baseToString(chars))) {
        return string;
      }
      var strSymbols = stringToArray(string),
          start = charsStartIndex(strSymbols, stringToArray(chars));

      return castSlice(strSymbols, start).join('');
    }

    /**
     * Truncates `string` if it's longer than the given maximum string length.
     * The last characters of the truncated string are replaced with the omission
     * string which defaults to "...".
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category String
     * @param {string} [string=''] The string to truncate.
     * @param {Object} [options={}] The options object.
     * @param {number} [options.length=30] The maximum string length.
     * @param {string} [options.omission='...'] The string to indicate text is omitted.
     * @param {RegExp|string} [options.separator] The separator pattern to truncate to.
     * @returns {string} Returns the truncated string.
     * @example
     *
     * _.truncate('hi-diddly-ho there, neighborino');
     * // => 'hi-diddly-ho there, neighbo...'
     *
     * _.truncate('hi-diddly-ho there, neighborino', {
     *   'length': 24,
     *   'separator': ' '
     * });
     * // => 'hi-diddly-ho there,...'
     *
     * _.truncate('hi-diddly-ho there, neighborino', {
     *   'length': 24,
     *   'separator': /,? +/
     * });
     * // => 'hi-diddly-ho there...'
     *
     * _.truncate('hi-diddly-ho there, neighborino', {
     *   'omission': ' [...]'
     * });
     * // => 'hi-diddly-ho there, neig [...]'
     */
    function truncate(string, options) {
      var length = DEFAULT_TRUNC_LENGTH,
          omission = DEFAULT_TRUNC_OMISSION;

      if (isObject(options)) {
        var separator = 'separator' in options ? options.separator : separator;
        length = 'length' in options ? toInteger(options.length) : length;
        omission = 'omission' in options ? baseToString(options.omission) : omission;
      }
      string = toString(string);

      var strLength = string.length;
      if (hasUnicode(string)) {
        var strSymbols = stringToArray(string);
        strLength = strSymbols.length;
      }
      if (length >= strLength) {
        return string;
      }
      var end = length - stringSize(omission);
      if (end < 1) {
        return omission;
      }
      var result = strSymbols
        ? castSlice(strSymbols, 0, end).join('')
        : string.slice(0, end);

      if (separator === undefined) {
        return result + omission;
      }
      if (strSymbols) {
        end += (result.length - end);
      }
      if (isRegExp(separator)) {
        if (string.slice(end).search(separator)) {
          var match,
              substring = result;

          if (!separator.global) {
            separator = RegExp(separator.source, toString(reFlags.exec(separator)) + 'g');
          }
          separator.lastIndex = 0;
          while ((match = separator.exec(substring))) {
            var newEnd = match.index;
          }
          result = result.slice(0, newEnd === undefined ? end : newEnd);
        }
      } else if (string.indexOf(baseToString(separator), end) != end) {
        var index = result.lastIndexOf(separator);
        if (index > -1) {
          result = result.slice(0, index);
        }
      }
      return result + omission;
    }

    /**
     * The inverse of `_.escape`; this method converts the HTML entities
     * `&amp;`, `&lt;`, `&gt;`, `&quot;`, and `&#39;` in `string` to
     * their corresponding characters.
     *
     * **Note:** No other HTML entities are unescaped. To unescape additional
     * HTML entities use a third-party library like [_he_](https://mths.be/he).
     *
     * @static
     * @memberOf _
     * @since 0.6.0
     * @category String
     * @param {string} [string=''] The string to unescape.
     * @returns {string} Returns the unescaped string.
     * @example
     *
     * _.unescape('fred, barney, &amp; pebbles');
     * // => 'fred, barney, & pebbles'
     */
    function unescape(string) {
      string = toString(string);
      return (string && reHasEscapedHtml.test(string))
        ? string.replace(reEscapedHtml, unescapeHtmlChar)
        : string;
    }

    /**
     * Converts `string`, as space separated words, to upper case.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category String
     * @param {string} [string=''] The string to convert.
     * @returns {string} Returns the upper cased string.
     * @example
     *
     * _.upperCase('--foo-bar');
     * // => 'FOO BAR'
     *
     * _.upperCase('fooBar');
     * // => 'FOO BAR'
     *
     * _.upperCase('__foo_bar__');
     * // => 'FOO BAR'
     */
    var upperCase = createCompounder(function(result, word, index) {
      return result + (index ? ' ' : '') + word.toUpperCase();
    });

    /**
     * Converts the first character of `string` to upper case.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category String
     * @param {string} [string=''] The string to convert.
     * @returns {string} Returns the converted string.
     * @example
     *
     * _.upperFirst('fred');
     * // => 'Fred'
     *
     * _.upperFirst('FRED');
     * // => 'FRED'
     */
    var upperFirst = createCaseFirst('toUpperCase');

    /**
     * Splits `string` into an array of its words.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category String
     * @param {string} [string=''] The string to inspect.
     * @param {RegExp|string} [pattern] The pattern to match words.
     * @param- {Object} [guard] Enables use as an iteratee for methods like `_.map`.
     * @returns {Array} Returns the words of `string`.
     * @example
     *
     * _.words('fred, barney, & pebbles');
     * // => ['fred', 'barney', 'pebbles']
     *
     * _.words('fred, barney, & pebbles', /[^, ]+/g);
     * // => ['fred', 'barney', '&', 'pebbles']
     */
    function words(string, pattern, guard) {
      string = toString(string);
      pattern = guard ? undefined : pattern;

      if (pattern === undefined) {
        return hasUnicodeWord(string) ? unicodeWords(string) : asciiWords(string);
      }
      return string.match(pattern) || [];
    }

    /*------------------------------------------------------------------------*/

    /**
     * Attempts to invoke `func`, returning either the result or the caught error
     * object. Any additional arguments are provided to `func` when it's invoked.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Util
     * @param {Function} func The function to attempt.
     * @param {...*} [args] The arguments to invoke `func` with.
     * @returns {*} Returns the `func` result or error object.
     * @example
     *
     * // Avoid throwing errors for invalid selectors.
     * var elements = _.attempt(function(selector) {
     *   return document.querySelectorAll(selector);
     * }, '>_>');
     *
     * if (_.isError(elements)) {
     *   elements = [];
     * }
     */
    var attempt = baseRest(function(func, args) {
      try {
        return apply(func, undefined, args);
      } catch (e) {
        return isError(e) ? e : new Error(e);
      }
    });

    /**
     * Binds methods of an object to the object itself, overwriting the existing
     * method.
     *
     * **Note:** This method doesn't set the "length" property of bound functions.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Util
     * @param {Object} object The object to bind and assign the bound methods to.
     * @param {...(string|string[])} methodNames The object method names to bind.
     * @returns {Object} Returns `object`.
     * @example
     *
     * var view = {
     *   'label': 'docs',
     *   'click': function() {
     *     console.log('clicked ' + this.label);
     *   }
     * };
     *
     * _.bindAll(view, ['click']);
     * jQuery(element).on('click', view.click);
     * // => Logs 'clicked docs' when clicked.
     */
    var bindAll = flatRest(function(object, methodNames) {
      arrayEach(methodNames, function(key) {
        key = toKey(key);
        baseAssignValue(object, key, bind(object[key], object));
      });
      return object;
    });

    /**
     * Creates a function that iterates over `pairs` and invokes the corresponding
     * function of the first predicate to return truthy. The predicate-function
     * pairs are invoked with the `this` binding and arguments of the created
     * function.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Util
     * @param {Array} pairs The predicate-function pairs.
     * @returns {Function} Returns the new composite function.
     * @example
     *
     * var func = _.cond([
     *   [_.matches({ 'a': 1 }),           _.constant('matches A')],
     *   [_.conforms({ 'b': _.isNumber }), _.constant('matches B')],
     *   [_.stubTrue,                      _.constant('no match')]
     * ]);
     *
     * func({ 'a': 1, 'b': 2 });
     * // => 'matches A'
     *
     * func({ 'a': 0, 'b': 1 });
     * // => 'matches B'
     *
     * func({ 'a': '1', 'b': '2' });
     * // => 'no match'
     */
    function cond(pairs) {
      var length = pairs == null ? 0 : pairs.length,
          toIteratee = getIteratee();

      pairs = !length ? [] : arrayMap(pairs, function(pair) {
        if (typeof pair[1] != 'function') {
          throw new TypeError(FUNC_ERROR_TEXT);
        }
        return [toIteratee(pair[0]), pair[1]];
      });

      return baseRest(function(args) {
        var index = -1;
        while (++index < length) {
          var pair = pairs[index];
          if (apply(pair[0], this, args)) {
            return apply(pair[1], this, args);
          }
        }
      });
    }

    /**
     * Creates a function that invokes the predicate properties of `source` with
     * the corresponding property values of a given object, returning `true` if
     * all predicates return truthy, else `false`.
     *
     * **Note:** The created function is equivalent to `_.conformsTo` with
     * `source` partially applied.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Util
     * @param {Object} source The object of property predicates to conform to.
     * @returns {Function} Returns the new spec function.
     * @example
     *
     * var objects = [
     *   { 'a': 2, 'b': 1 },
     *   { 'a': 1, 'b': 2 }
     * ];
     *
     * _.filter(objects, _.conforms({ 'b': function(n) { return n > 1; } }));
     * // => [{ 'a': 1, 'b': 2 }]
     */
    function conforms(source) {
      return baseConforms(baseClone(source, CLONE_DEEP_FLAG));
    }

    /**
     * Creates a function that returns `value`.
     *
     * @static
     * @memberOf _
     * @since 2.4.0
     * @category Util
     * @param {*} value The value to return from the new function.
     * @returns {Function} Returns the new constant function.
     * @example
     *
     * var objects = _.times(2, _.constant({ 'a': 1 }));
     *
     * console.log(objects);
     * // => [{ 'a': 1 }, { 'a': 1 }]
     *
     * console.log(objects[0] === objects[1]);
     * // => true
     */
    function constant(value) {
      return function() {
        return value;
      };
    }

    /**
     * Checks `value` to determine whether a default value should be returned in
     * its place. The `defaultValue` is returned if `value` is `NaN`, `null`,
     * or `undefined`.
     *
     * @static
     * @memberOf _
     * @since 4.14.0
     * @category Util
     * @param {*} value The value to check.
     * @param {*} defaultValue The default value.
     * @returns {*} Returns the resolved value.
     * @example
     *
     * _.defaultTo(1, 10);
     * // => 1
     *
     * _.defaultTo(undefined, 10);
     * // => 10
     */
    function defaultTo(value, defaultValue) {
      return (value == null || value !== value) ? defaultValue : value;
    }

    /**
     * Creates a function that returns the result of invoking the given functions
     * with the `this` binding of the created function, where each successive
     * invocation is supplied the return value of the previous.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Util
     * @param {...(Function|Function[])} [funcs] The functions to invoke.
     * @returns {Function} Returns the new composite function.
     * @see _.flowRight
     * @example
     *
     * function square(n) {
     *   return n * n;
     * }
     *
     * var addSquare = _.flow([_.add, square]);
     * addSquare(1, 2);
     * // => 9
     */
    var flow = createFlow();

    /**
     * This method is like `_.flow` except that it creates a function that
     * invokes the given functions from right to left.
     *
     * @static
     * @since 3.0.0
     * @memberOf _
     * @category Util
     * @param {...(Function|Function[])} [funcs] The functions to invoke.
     * @returns {Function} Returns the new composite function.
     * @see _.flow
     * @example
     *
     * function square(n) {
     *   return n * n;
     * }
     *
     * var addSquare = _.flowRight([square, _.add]);
     * addSquare(1, 2);
     * // => 9
     */
    var flowRight = createFlow(true);

    /**
     * This method returns the first argument it receives.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Util
     * @param {*} value Any value.
     * @returns {*} Returns `value`.
     * @example
     *
     * var object = { 'a': 1 };
     *
     * console.log(_.identity(object) === object);
     * // => true
     */
    function identity(value) {
      return value;
    }

    /**
     * Creates a function that invokes `func` with the arguments of the created
     * function. If `func` is a property name, the created function returns the
     * property value for a given element. If `func` is an array or object, the
     * created function returns `true` for elements that contain the equivalent
     * source properties, otherwise it returns `false`.
     *
     * @static
     * @since 4.0.0
     * @memberOf _
     * @category Util
     * @param {*} [func=_.identity] The value to convert to a callback.
     * @returns {Function} Returns the callback.
     * @example
     *
     * var users = [
     *   { 'user': 'barney', 'age': 36, 'active': true },
     *   { 'user': 'fred',   'age': 40, 'active': false }
     * ];
     *
     * // The `_.matches` iteratee shorthand.
     * _.filter(users, _.iteratee({ 'user': 'barney', 'active': true }));
     * // => [{ 'user': 'barney', 'age': 36, 'active': true }]
     *
     * // The `_.matchesProperty` iteratee shorthand.
     * _.filter(users, _.iteratee(['user', 'fred']));
     * // => [{ 'user': 'fred', 'age': 40 }]
     *
     * // The `_.property` iteratee shorthand.
     * _.map(users, _.iteratee('user'));
     * // => ['barney', 'fred']
     *
     * // Create custom iteratee shorthands.
     * _.iteratee = _.wrap(_.iteratee, function(iteratee, func) {
     *   return !_.isRegExp(func) ? iteratee(func) : function(string) {
     *     return func.test(string);
     *   };
     * });
     *
     * _.filter(['abc', 'def'], /ef/);
     * // => ['def']
     */
    function iteratee(func) {
      return baseIteratee(typeof func == 'function' ? func : baseClone(func, CLONE_DEEP_FLAG));
    }

    /**
     * Creates a function that performs a partial deep comparison between a given
     * object and `source`, returning `true` if the given object has equivalent
     * property values, else `false`.
     *
     * **Note:** The created function is equivalent to `_.isMatch` with `source`
     * partially applied.
     *
     * Partial comparisons will match empty array and empty object `source`
     * values against any array or object value, respectively. See `_.isEqual`
     * for a list of supported value comparisons.
     *
     * **Note:** Multiple values can be checked by combining several matchers
     * using `_.overSome`
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Util
     * @param {Object} source The object of property values to match.
     * @returns {Function} Returns the new spec function.
     * @example
     *
     * var objects = [
     *   { 'a': 1, 'b': 2, 'c': 3 },
     *   { 'a': 4, 'b': 5, 'c': 6 }
     * ];
     *
     * _.filter(objects, _.matches({ 'a': 4, 'c': 6 }));
     * // => [{ 'a': 4, 'b': 5, 'c': 6 }]
     *
     * // Checking for several possible values
     * _.filter(objects, _.overSome([_.matches({ 'a': 1 }), _.matches({ 'a': 4 })]));
     * // => [{ 'a': 1, 'b': 2, 'c': 3 }, { 'a': 4, 'b': 5, 'c': 6 }]
     */
    function matches(source) {
      return baseMatches(baseClone(source, CLONE_DEEP_FLAG));
    }

    /**
     * Creates a function that performs a partial deep comparison between the
     * value at `path` of a given object to `srcValue`, returning `true` if the
     * object value is equivalent, else `false`.
     *
     * **Note:** Partial comparisons will match empty array and empty object
     * `srcValue` values against any array or object value, respectively. See
     * `_.isEqual` for a list of supported value comparisons.
     *
     * **Note:** Multiple values can be checked by combining several matchers
     * using `_.overSome`
     *
     * @static
     * @memberOf _
     * @since 3.2.0
     * @category Util
     * @param {Array|string} path The path of the property to get.
     * @param {*} srcValue The value to match.
     * @returns {Function} Returns the new spec function.
     * @example
     *
     * var objects = [
     *   { 'a': 1, 'b': 2, 'c': 3 },
     *   { 'a': 4, 'b': 5, 'c': 6 }
     * ];
     *
     * _.find(objects, _.matchesProperty('a', 4));
     * // => { 'a': 4, 'b': 5, 'c': 6 }
     *
     * // Checking for several possible values
     * _.filter(objects, _.overSome([_.matchesProperty('a', 1), _.matchesProperty('a', 4)]));
     * // => [{ 'a': 1, 'b': 2, 'c': 3 }, { 'a': 4, 'b': 5, 'c': 6 }]
     */
    function matchesProperty(path, srcValue) {
      return baseMatchesProperty(path, baseClone(srcValue, CLONE_DEEP_FLAG));
    }

    /**
     * Creates a function that invokes the method at `path` of a given object.
     * Any additional arguments are provided to the invoked method.
     *
     * @static
     * @memberOf _
     * @since 3.7.0
     * @category Util
     * @param {Array|string} path The path of the method to invoke.
     * @param {...*} [args] The arguments to invoke the method with.
     * @returns {Function} Returns the new invoker function.
     * @example
     *
     * var objects = [
     *   { 'a': { 'b': _.constant(2) } },
     *   { 'a': { 'b': _.constant(1) } }
     * ];
     *
     * _.map(objects, _.method('a.b'));
     * // => [2, 1]
     *
     * _.map(objects, _.method(['a', 'b']));
     * // => [2, 1]
     */
    var method = baseRest(function(path, args) {
      return function(object) {
        return baseInvoke(object, path, args);
      };
    });

    /**
     * The opposite of `_.method`; this method creates a function that invokes
     * the method at a given path of `object`. Any additional arguments are
     * provided to the invoked method.
     *
     * @static
     * @memberOf _
     * @since 3.7.0
     * @category Util
     * @param {Object} object The object to query.
     * @param {...*} [args] The arguments to invoke the method with.
     * @returns {Function} Returns the new invoker function.
     * @example
     *
     * var array = _.times(3, _.constant),
     *     object = { 'a': array, 'b': array, 'c': array };
     *
     * _.map(['a[2]', 'c[0]'], _.methodOf(object));
     * // => [2, 0]
     *
     * _.map([['a', '2'], ['c', '0']], _.methodOf(object));
     * // => [2, 0]
     */
    var methodOf = baseRest(function(object, args) {
      return function(path) {
        return baseInvoke(object, path, args);
      };
    });

    /**
     * Adds all own enumerable string keyed function properties of a source
     * object to the destination object. If `object` is a function, then methods
     * are added to its prototype as well.
     *
     * **Note:** Use `_.runInContext` to create a pristine `lodash` function to
     * avoid conflicts caused by modifying the original.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Util
     * @param {Function|Object} [object=lodash] The destination object.
     * @param {Object} source The object of functions to add.
     * @param {Object} [options={}] The options object.
     * @param {boolean} [options.chain=true] Specify whether mixins are chainable.
     * @returns {Function|Object} Returns `object`.
     * @example
     *
     * function vowels(string) {
     *   return _.filter(string, function(v) {
     *     return /[aeiou]/i.test(v);
     *   });
     * }
     *
     * _.mixin({ 'vowels': vowels });
     * _.vowels('fred');
     * // => ['e']
     *
     * _('fred').vowels().value();
     * // => ['e']
     *
     * _.mixin({ 'vowels': vowels }, { 'chain': false });
     * _('fred').vowels();
     * // => ['e']
     */
    function mixin(object, source, options) {
      var props = keys(source),
          methodNames = baseFunctions(source, props);

      if (options == null &&
          !(isObject(source) && (methodNames.length || !props.length))) {
        options = source;
        source = object;
        object = this;
        methodNames = baseFunctions(source, keys(source));
      }
      var chain = !(isObject(options) && 'chain' in options) || !!options.chain,
          isFunc = isFunction(object);

      arrayEach(methodNames, function(methodName) {
        var func = source[methodName];
        object[methodName] = func;
        if (isFunc) {
          object.prototype[methodName] = function() {
            var chainAll = this.__chain__;
            if (chain || chainAll) {
              var result = object(this.__wrapped__),
                  actions = result.__actions__ = copyArray(this.__actions__);

              actions.push({ 'func': func, 'args': arguments, 'thisArg': object });
              result.__chain__ = chainAll;
              return result;
            }
            return func.apply(object, arrayPush([this.value()], arguments));
          };
        }
      });

      return object;
    }

    /**
     * Reverts the `_` variable to its previous value and returns a reference to
     * the `lodash` function.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Util
     * @returns {Function} Returns the `lodash` function.
     * @example
     *
     * var lodash = _.noConflict();
     */
    function noConflict() {
      if (root._ === this) {
        root._ = oldDash;
      }
      return this;
    }

    /**
     * This method returns `undefined`.
     *
     * @static
     * @memberOf _
     * @since 2.3.0
     * @category Util
     * @example
     *
     * _.times(2, _.noop);
     * // => [undefined, undefined]
     */
    function noop() {
      // No operation performed.
    }

    /**
     * Creates a function that gets the argument at index `n`. If `n` is negative,
     * the nth argument from the end is returned.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Util
     * @param {number} [n=0] The index of the argument to return.
     * @returns {Function} Returns the new pass-thru function.
     * @example
     *
     * var func = _.nthArg(1);
     * func('a', 'b', 'c', 'd');
     * // => 'b'
     *
     * var func = _.nthArg(-2);
     * func('a', 'b', 'c', 'd');
     * // => 'c'
     */
    function nthArg(n) {
      n = toInteger(n);
      return baseRest(function(args) {
        return baseNth(args, n);
      });
    }

    /**
     * Creates a function that invokes `iteratees` with the arguments it receives
     * and returns their results.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Util
     * @param {...(Function|Function[])} [iteratees=[_.identity]]
     *  The iteratees to invoke.
     * @returns {Function} Returns the new function.
     * @example
     *
     * var func = _.over([Math.max, Math.min]);
     *
     * func(1, 2, 3, 4);
     * // => [4, 1]
     */
    var over = createOver(arrayMap);

    /**
     * Creates a function that checks if **all** of the `predicates` return
     * truthy when invoked with the arguments it receives.
     *
     * Following shorthands are possible for providing predicates.
     * Pass an `Object` and it will be used as an parameter for `_.matches` to create the predicate.
     * Pass an `Array` of parameters for `_.matchesProperty` and the predicate will be created using them.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Util
     * @param {...(Function|Function[])} [predicates=[_.identity]]
     *  The predicates to check.
     * @returns {Function} Returns the new function.
     * @example
     *
     * var func = _.overEvery([Boolean, isFinite]);
     *
     * func('1');
     * // => true
     *
     * func(null);
     * // => false
     *
     * func(NaN);
     * // => false
     */
    var overEvery = createOver(arrayEvery);

    /**
     * Creates a function that checks if **any** of the `predicates` return
     * truthy when invoked with the arguments it receives.
     *
     * Following shorthands are possible for providing predicates.
     * Pass an `Object` and it will be used as an parameter for `_.matches` to create the predicate.
     * Pass an `Array` of parameters for `_.matchesProperty` and the predicate will be created using them.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Util
     * @param {...(Function|Function[])} [predicates=[_.identity]]
     *  The predicates to check.
     * @returns {Function} Returns the new function.
     * @example
     *
     * var func = _.overSome([Boolean, isFinite]);
     *
     * func('1');
     * // => true
     *
     * func(null);
     * // => true
     *
     * func(NaN);
     * // => false
     *
     * var matchesFunc = _.overSome([{ 'a': 1 }, { 'a': 2 }])
     * var matchesPropertyFunc = _.overSome([['a', 1], ['a', 2]])
     */
    var overSome = createOver(arraySome);

    /**
     * Creates a function that returns the value at `path` of a given object.
     *
     * @static
     * @memberOf _
     * @since 2.4.0
     * @category Util
     * @param {Array|string} path The path of the property to get.
     * @returns {Function} Returns the new accessor function.
     * @example
     *
     * var objects = [
     *   { 'a': { 'b': 2 } },
     *   { 'a': { 'b': 1 } }
     * ];
     *
     * _.map(objects, _.property('a.b'));
     * // => [2, 1]
     *
     * _.map(_.sortBy(objects, _.property(['a', 'b'])), 'a.b');
     * // => [1, 2]
     */
    function property(path) {
      return isKey(path) ? baseProperty(toKey(path)) : basePropertyDeep(path);
    }

    /**
     * The opposite of `_.property`; this method creates a function that returns
     * the value at a given path of `object`.
     *
     * @static
     * @memberOf _
     * @since 3.0.0
     * @category Util
     * @param {Object} object The object to query.
     * @returns {Function} Returns the new accessor function.
     * @example
     *
     * var array = [0, 1, 2],
     *     object = { 'a': array, 'b': array, 'c': array };
     *
     * _.map(['a[2]', 'c[0]'], _.propertyOf(object));
     * // => [2, 0]
     *
     * _.map([['a', '2'], ['c', '0']], _.propertyOf(object));
     * // => [2, 0]
     */
    function propertyOf(object) {
      return function(path) {
        return object == null ? undefined : baseGet(object, path);
      };
    }

    /**
     * Creates an array of numbers (positive and/or negative) progressing from
     * `start` up to, but not including, `end`. A step of `-1` is used if a negative
     * `start` is specified without an `end` or `step`. If `end` is not specified,
     * it's set to `start` with `start` then set to `0`.
     *
     * **Note:** JavaScript follows the IEEE-754 standard for resolving
     * floating-point values which can produce unexpected results.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Util
     * @param {number} [start=0] The start of the range.
     * @param {number} end The end of the range.
     * @param {number} [step=1] The value to increment or decrement by.
     * @returns {Array} Returns the range of numbers.
     * @see _.inRange, _.rangeRight
     * @example
     *
     * _.range(4);
     * // => [0, 1, 2, 3]
     *
     * _.range(-4);
     * // => [0, -1, -2, -3]
     *
     * _.range(1, 5);
     * // => [1, 2, 3, 4]
     *
     * _.range(0, 20, 5);
     * // => [0, 5, 10, 15]
     *
     * _.range(0, -4, -1);
     * // => [0, -1, -2, -3]
     *
     * _.range(1, 4, 0);
     * // => [1, 1, 1]
     *
     * _.range(0);
     * // => []
     */
    var range = createRange();

    /**
     * This method is like `_.range` except that it populates values in
     * descending order.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Util
     * @param {number} [start=0] The start of the range.
     * @param {number} end The end of the range.
     * @param {number} [step=1] The value to increment or decrement by.
     * @returns {Array} Returns the range of numbers.
     * @see _.inRange, _.range
     * @example
     *
     * _.rangeRight(4);
     * // => [3, 2, 1, 0]
     *
     * _.rangeRight(-4);
     * // => [-3, -2, -1, 0]
     *
     * _.rangeRight(1, 5);
     * // => [4, 3, 2, 1]
     *
     * _.rangeRight(0, 20, 5);
     * // => [15, 10, 5, 0]
     *
     * _.rangeRight(0, -4, -1);
     * // => [-3, -2, -1, 0]
     *
     * _.rangeRight(1, 4, 0);
     * // => [1, 1, 1]
     *
     * _.rangeRight(0);
     * // => []
     */
    var rangeRight = createRange(true);

    /**
     * This method returns a new empty array.
     *
     * @static
     * @memberOf _
     * @since 4.13.0
     * @category Util
     * @returns {Array} Returns the new empty array.
     * @example
     *
     * var arrays = _.times(2, _.stubArray);
     *
     * console.log(arrays);
     * // => [[], []]
     *
     * console.log(arrays[0] === arrays[1]);
     * // => false
     */
    function stubArray() {
      return [];
    }

    /**
     * This method returns `false`.
     *
     * @static
     * @memberOf _
     * @since 4.13.0
     * @category Util
     * @returns {boolean} Returns `false`.
     * @example
     *
     * _.times(2, _.stubFalse);
     * // => [false, false]
     */
    function stubFalse() {
      return false;
    }

    /**
     * This method returns a new empty object.
     *
     * @static
     * @memberOf _
     * @since 4.13.0
     * @category Util
     * @returns {Object} Returns the new empty object.
     * @example
     *
     * var objects = _.times(2, _.stubObject);
     *
     * console.log(objects);
     * // => [{}, {}]
     *
     * console.log(objects[0] === objects[1]);
     * // => false
     */
    function stubObject() {
      return {};
    }

    /**
     * This method returns an empty string.
     *
     * @static
     * @memberOf _
     * @since 4.13.0
     * @category Util
     * @returns {string} Returns the empty string.
     * @example
     *
     * _.times(2, _.stubString);
     * // => ['', '']
     */
    function stubString() {
      return '';
    }

    /**
     * This method returns `true`.
     *
     * @static
     * @memberOf _
     * @since 4.13.0
     * @category Util
     * @returns {boolean} Returns `true`.
     * @example
     *
     * _.times(2, _.stubTrue);
     * // => [true, true]
     */
    function stubTrue() {
      return true;
    }

    /**
     * Invokes the iteratee `n` times, returning an array of the results of
     * each invocation. The iteratee is invoked with one argument; (index).
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Util
     * @param {number} n The number of times to invoke `iteratee`.
     * @param {Function} [iteratee=_.identity] The function invoked per iteration.
     * @returns {Array} Returns the array of results.
     * @example
     *
     * _.times(3, String);
     * // => ['0', '1', '2']
     *
     *  _.times(4, _.constant(0));
     * // => [0, 0, 0, 0]
     */
    function times(n, iteratee) {
      n = toInteger(n);
      if (n < 1 || n > MAX_SAFE_INTEGER) {
        return [];
      }
      var index = MAX_ARRAY_LENGTH,
          length = nativeMin(n, MAX_ARRAY_LENGTH);

      iteratee = getIteratee(iteratee);
      n -= MAX_ARRAY_LENGTH;

      var result = baseTimes(length, iteratee);
      while (++index < n) {
        iteratee(index);
      }
      return result;
    }

    /**
     * Converts `value` to a property path array.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Util
     * @param {*} value The value to convert.
     * @returns {Array} Returns the new property path array.
     * @example
     *
     * _.toPath('a.b.c');
     * // => ['a', 'b', 'c']
     *
     * _.toPath('a[0].b.c');
     * // => ['a', '0', 'b', 'c']
     */
    function toPath(value) {
      if (isArray(value)) {
        return arrayMap(value, toKey);
      }
      return isSymbol(value) ? [value] : copyArray(stringToPath(toString(value)));
    }

    /**
     * Generates a unique ID. If `prefix` is given, the ID is appended to it.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Util
     * @param {string} [prefix=''] The value to prefix the ID with.
     * @returns {string} Returns the unique ID.
     * @example
     *
     * _.uniqueId('contact_');
     * // => 'contact_104'
     *
     * _.uniqueId();
     * // => '105'
     */
    function uniqueId(prefix) {
      var id = ++idCounter;
      return toString(prefix) + id;
    }

    /*------------------------------------------------------------------------*/

    /**
     * Adds two numbers.
     *
     * @static
     * @memberOf _
     * @since 3.4.0
     * @category Math
     * @param {number} augend The first number in an addition.
     * @param {number} addend The second number in an addition.
     * @returns {number} Returns the total.
     * @example
     *
     * _.add(6, 4);
     * // => 10
     */
    var add = createMathOperation(function(augend, addend) {
      return augend + addend;
    }, 0);

    /**
     * Computes `number` rounded up to `precision`.
     *
     * @static
     * @memberOf _
     * @since 3.10.0
     * @category Math
     * @param {number} number The number to round up.
     * @param {number} [precision=0] The precision to round up to.
     * @returns {number} Returns the rounded up number.
     * @example
     *
     * _.ceil(4.006);
     * // => 5
     *
     * _.ceil(6.004, 2);
     * // => 6.01
     *
     * _.ceil(6040, -2);
     * // => 6100
     */
    var ceil = createRound('ceil');

    /**
     * Divide two numbers.
     *
     * @static
     * @memberOf _
     * @since 4.7.0
     * @category Math
     * @param {number} dividend The first number in a division.
     * @param {number} divisor The second number in a division.
     * @returns {number} Returns the quotient.
     * @example
     *
     * _.divide(6, 4);
     * // => 1.5
     */
    var divide = createMathOperation(function(dividend, divisor) {
      return dividend / divisor;
    }, 1);

    /**
     * Computes `number` rounded down to `precision`.
     *
     * @static
     * @memberOf _
     * @since 3.10.0
     * @category Math
     * @param {number} number The number to round down.
     * @param {number} [precision=0] The precision to round down to.
     * @returns {number} Returns the rounded down number.
     * @example
     *
     * _.floor(4.006);
     * // => 4
     *
     * _.floor(0.046, 2);
     * // => 0.04
     *
     * _.floor(4060, -2);
     * // => 4000
     */
    var floor = createRound('floor');

    /**
     * Computes the maximum value of `array`. If `array` is empty or falsey,
     * `undefined` is returned.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Math
     * @param {Array} array The array to iterate over.
     * @returns {*} Returns the maximum value.
     * @example
     *
     * _.max([4, 2, 8, 6]);
     * // => 8
     *
     * _.max([]);
     * // => undefined
     */
    function max(array) {
      return (array && array.length)
        ? baseExtremum(array, identity, baseGt)
        : undefined;
    }

    /**
     * This method is like `_.max` except that it accepts `iteratee` which is
     * invoked for each element in `array` to generate the criterion by which
     * the value is ranked. The iteratee is invoked with one argument: (value).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Math
     * @param {Array} array The array to iterate over.
     * @param {Function} [iteratee=_.identity] The iteratee invoked per element.
     * @returns {*} Returns the maximum value.
     * @example
     *
     * var objects = [{ 'n': 1 }, { 'n': 2 }];
     *
     * _.maxBy(objects, function(o) { return o.n; });
     * // => { 'n': 2 }
     *
     * // The `_.property` iteratee shorthand.
     * _.maxBy(objects, 'n');
     * // => { 'n': 2 }
     */
    function maxBy(array, iteratee) {
      return (array && array.length)
        ? baseExtremum(array, getIteratee(iteratee, 2), baseGt)
        : undefined;
    }

    /**
     * Computes the mean of the values in `array`.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Math
     * @param {Array} array The array to iterate over.
     * @returns {number} Returns the mean.
     * @example
     *
     * _.mean([4, 2, 8, 6]);
     * // => 5
     */
    function mean(array) {
      return baseMean(array, identity);
    }

    /**
     * This method is like `_.mean` except that it accepts `iteratee` which is
     * invoked for each element in `array` to generate the value to be averaged.
     * The iteratee is invoked with one argument: (value).
     *
     * @static
     * @memberOf _
     * @since 4.7.0
     * @category Math
     * @param {Array} array The array to iterate over.
     * @param {Function} [iteratee=_.identity] The iteratee invoked per element.
     * @returns {number} Returns the mean.
     * @example
     *
     * var objects = [{ 'n': 4 }, { 'n': 2 }, { 'n': 8 }, { 'n': 6 }];
     *
     * _.meanBy(objects, function(o) { return o.n; });
     * // => 5
     *
     * // The `_.property` iteratee shorthand.
     * _.meanBy(objects, 'n');
     * // => 5
     */
    function meanBy(array, iteratee) {
      return baseMean(array, getIteratee(iteratee, 2));
    }

    /**
     * Computes the minimum value of `array`. If `array` is empty or falsey,
     * `undefined` is returned.
     *
     * @static
     * @since 0.1.0
     * @memberOf _
     * @category Math
     * @param {Array} array The array to iterate over.
     * @returns {*} Returns the minimum value.
     * @example
     *
     * _.min([4, 2, 8, 6]);
     * // => 2
     *
     * _.min([]);
     * // => undefined
     */
    function min(array) {
      return (array && array.length)
        ? baseExtremum(array, identity, baseLt)
        : undefined;
    }

    /**
     * This method is like `_.min` except that it accepts `iteratee` which is
     * invoked for each element in `array` to generate the criterion by which
     * the value is ranked. The iteratee is invoked with one argument: (value).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Math
     * @param {Array} array The array to iterate over.
     * @param {Function} [iteratee=_.identity] The iteratee invoked per element.
     * @returns {*} Returns the minimum value.
     * @example
     *
     * var objects = [{ 'n': 1 }, { 'n': 2 }];
     *
     * _.minBy(objects, function(o) { return o.n; });
     * // => { 'n': 1 }
     *
     * // The `_.property` iteratee shorthand.
     * _.minBy(objects, 'n');
     * // => { 'n': 1 }
     */
    function minBy(array, iteratee) {
      return (array && array.length)
        ? baseExtremum(array, getIteratee(iteratee, 2), baseLt)
        : undefined;
    }

    /**
     * Multiply two numbers.
     *
     * @static
     * @memberOf _
     * @since 4.7.0
     * @category Math
     * @param {number} multiplier The first number in a multiplication.
     * @param {number} multiplicand The second number in a multiplication.
     * @returns {number} Returns the product.
     * @example
     *
     * _.multiply(6, 4);
     * // => 24
     */
    var multiply = createMathOperation(function(multiplier, multiplicand) {
      return multiplier * multiplicand;
    }, 1);

    /**
     * Computes `number` rounded to `precision`.
     *
     * @static
     * @memberOf _
     * @since 3.10.0
     * @category Math
     * @param {number} number The number to round.
     * @param {number} [precision=0] The precision to round to.
     * @returns {number} Returns the rounded number.
     * @example
     *
     * _.round(4.006);
     * // => 4
     *
     * _.round(4.006, 2);
     * // => 4.01
     *
     * _.round(4060, -2);
     * // => 4100
     */
    var round = createRound('round');

    /**
     * Subtract two numbers.
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Math
     * @param {number} minuend The first number in a subtraction.
     * @param {number} subtrahend The second number in a subtraction.
     * @returns {number} Returns the difference.
     * @example
     *
     * _.subtract(6, 4);
     * // => 2
     */
    var subtract = createMathOperation(function(minuend, subtrahend) {
      return minuend - subtrahend;
    }, 0);

    /**
     * Computes the sum of the values in `array`.
     *
     * @static
     * @memberOf _
     * @since 3.4.0
     * @category Math
     * @param {Array} array The array to iterate over.
     * @returns {number} Returns the sum.
     * @example
     *
     * _.sum([4, 2, 8, 6]);
     * // => 20
     */
    function sum(array) {
      return (array && array.length)
        ? baseSum(array, identity)
        : 0;
    }

    /**
     * This method is like `_.sum` except that it accepts `iteratee` which is
     * invoked for each element in `array` to generate the value to be summed.
     * The iteratee is invoked with one argument: (value).
     *
     * @static
     * @memberOf _
     * @since 4.0.0
     * @category Math
     * @param {Array} array The array to iterate over.
     * @param {Function} [iteratee=_.identity] The iteratee invoked per element.
     * @returns {number} Returns the sum.
     * @example
     *
     * var objects = [{ 'n': 4 }, { 'n': 2 }, { 'n': 8 }, { 'n': 6 }];
     *
     * _.sumBy(objects, function(o) { return o.n; });
     * // => 20
     *
     * // The `_.property` iteratee shorthand.
     * _.sumBy(objects, 'n');
     * // => 20
     */
    function sumBy(array, iteratee) {
      return (array && array.length)
        ? baseSum(array, getIteratee(iteratee, 2))
        : 0;
    }

    /*------------------------------------------------------------------------*/

    // Add methods that return wrapped values in chain sequences.
    lodash.after = after;
    lodash.ary = ary;
    lodash.assign = assign;
    lodash.assignIn = assignIn;
    lodash.assignInWith = assignInWith;
    lodash.assignWith = assignWith;
    lodash.at = at;
    lodash.before = before;
    lodash.bind = bind;
    lodash.bindAll = bindAll;
    lodash.bindKey = bindKey;
    lodash.castArray = castArray;
    lodash.chain = chain;
    lodash.chunk = chunk;
    lodash.compact = compact;
    lodash.concat = concat;
    lodash.cond = cond;
    lodash.conforms = conforms;
    lodash.constant = constant;
    lodash.countBy = countBy;
    lodash.create = create;
    lodash.curry = curry;
    lodash.curryRight = curryRight;
    lodash.debounce = debounce;
    lodash.defaults = defaults;
    lodash.defaultsDeep = defaultsDeep;
    lodash.defer = defer;
    lodash.delay = delay;
    lodash.difference = difference;
    lodash.differenceBy = differenceBy;
    lodash.differenceWith = differenceWith;
    lodash.drop = drop;
    lodash.dropRight = dropRight;
    lodash.dropRightWhile = dropRightWhile;
    lodash.dropWhile = dropWhile;
    lodash.fill = fill;
    lodash.filter = filter;
    lodash.flatMap = flatMap;
    lodash.flatMapDeep = flatMapDeep;
    lodash.flatMapDepth = flatMapDepth;
    lodash.flatten = flatten;
    lodash.flattenDeep = flattenDeep;
    lodash.flattenDepth = flattenDepth;
    lodash.flip = flip;
    lodash.flow = flow;
    lodash.flowRight = flowRight;
    lodash.fromPairs = fromPairs;
    lodash.functions = functions;
    lodash.functionsIn = functionsIn;
    lodash.groupBy = groupBy;
    lodash.initial = initial;
    lodash.intersection = intersection;
    lodash.intersectionBy = intersectionBy;
    lodash.intersectionWith = intersectionWith;
    lodash.invert = invert;
    lodash.invertBy = invertBy;
    lodash.invokeMap = invokeMap;
    lodash.iteratee = iteratee;
    lodash.keyBy = keyBy;
    lodash.keys = keys;
    lodash.keysIn = keysIn;
    lodash.map = map;
    lodash.mapKeys = mapKeys;
    lodash.mapValues = mapValues;
    lodash.matches = matches;
    lodash.matchesProperty = matchesProperty;
    lodash.memoize = memoize;
    lodash.merge = merge;
    lodash.mergeWith = mergeWith;
    lodash.method = method;
    lodash.methodOf = methodOf;
    lodash.mixin = mixin;
    lodash.negate = negate;
    lodash.nthArg = nthArg;
    lodash.omit = omit;
    lodash.omitBy = omitBy;
    lodash.once = once;
    lodash.orderBy = orderBy;
    lodash.over = over;
    lodash.overArgs = overArgs;
    lodash.overEvery = overEvery;
    lodash.overSome = overSome;
    lodash.partial = partial;
    lodash.partialRight = partialRight;
    lodash.partition = partition;
    lodash.pick = pick;
    lodash.pickBy = pickBy;
    lodash.property = property;
    lodash.propertyOf = propertyOf;
    lodash.pull = pull;
    lodash.pullAll = pullAll;
    lodash.pullAllBy = pullAllBy;
    lodash.pullAllWith = pullAllWith;
    lodash.pullAt = pullAt;
    lodash.range = range;
    lodash.rangeRight = rangeRight;
    lodash.rearg = rearg;
    lodash.reject = reject;
    lodash.remove = remove;
    lodash.rest = rest;
    lodash.reverse = reverse;
    lodash.sampleSize = sampleSize;
    lodash.set = set;
    lodash.setWith = setWith;
    lodash.shuffle = shuffle;
    lodash.slice = slice;
    lodash.sortBy = sortBy;
    lodash.sortedUniq = sortedUniq;
    lodash.sortedUniqBy = sortedUniqBy;
    lodash.split = split;
    lodash.spread = spread;
    lodash.tail = tail;
    lodash.take = take;
    lodash.takeRight = takeRight;
    lodash.takeRightWhile = takeRightWhile;
    lodash.takeWhile = takeWhile;
    lodash.tap = tap;
    lodash.throttle = throttle;
    lodash.thru = thru;
    lodash.toArray = toArray;
    lodash.toPairs = toPairs;
    lodash.toPairsIn = toPairsIn;
    lodash.toPath = toPath;
    lodash.toPlainObject = toPlainObject;
    lodash.transform = transform;
    lodash.unary = unary;
    lodash.union = union;
    lodash.unionBy = unionBy;
    lodash.unionWith = unionWith;
    lodash.uniq = uniq;
    lodash.uniqBy = uniqBy;
    lodash.uniqWith = uniqWith;
    lodash.unset = unset;
    lodash.unzip = unzip;
    lodash.unzipWith = unzipWith;
    lodash.update = update;
    lodash.updateWith = updateWith;
    lodash.values = values;
    lodash.valuesIn = valuesIn;
    lodash.without = without;
    lodash.words = words;
    lodash.wrap = wrap;
    lodash.xor = xor;
    lodash.xorBy = xorBy;
    lodash.xorWith = xorWith;
    lodash.zip = zip;
    lodash.zipObject = zipObject;
    lodash.zipObjectDeep = zipObjectDeep;
    lodash.zipWith = zipWith;

    // Add aliases.
    lodash.entries = toPairs;
    lodash.entriesIn = toPairsIn;
    lodash.extend = assignIn;
    lodash.extendWith = assignInWith;

    // Add methods to `lodash.prototype`.
    mixin(lodash, lodash);

    /*------------------------------------------------------------------------*/

    // Add methods that return unwrapped values in chain sequences.
    lodash.add = add;
    lodash.attempt = attempt;
    lodash.camelCase = camelCase;
    lodash.capitalize = capitalize;
    lodash.ceil = ceil;
    lodash.clamp = clamp;
    lodash.clone = clone;
    lodash.cloneDeep = cloneDeep;
    lodash.cloneDeepWith = cloneDeepWith;
    lodash.cloneWith = cloneWith;
    lodash.conformsTo = conformsTo;
    lodash.deburr = deburr;
    lodash.defaultTo = defaultTo;
    lodash.divide = divide;
    lodash.endsWith = endsWith;
    lodash.eq = eq;
    lodash.escape = escape;
    lodash.escapeRegExp = escapeRegExp;
    lodash.every = every;
    lodash.find = find;
    lodash.findIndex = findIndex;
    lodash.findKey = findKey;
    lodash.findLast = findLast;
    lodash.findLastIndex = findLastIndex;
    lodash.findLastKey = findLastKey;
    lodash.floor = floor;
    lodash.forEach = forEach;
    lodash.forEachRight = forEachRight;
    lodash.forIn = forIn;
    lodash.forInRight = forInRight;
    lodash.forOwn = forOwn;
    lodash.forOwnRight = forOwnRight;
    lodash.get = get;
    lodash.gt = gt;
    lodash.gte = gte;
    lodash.has = has;
    lodash.hasIn = hasIn;
    lodash.head = head;
    lodash.identity = identity;
    lodash.includes = includes;
    lodash.indexOf = indexOf;
    lodash.inRange = inRange;
    lodash.invoke = invoke;
    lodash.isArguments = isArguments;
    lodash.isArray = isArray;
    lodash.isArrayBuffer = isArrayBuffer;
    lodash.isArrayLike = isArrayLike;
    lodash.isArrayLikeObject = isArrayLikeObject;
    lodash.isBoolean = isBoolean;
    lodash.isBuffer = isBuffer;
    lodash.isDate = isDate;
    lodash.isElement = isElement;
    lodash.isEmpty = isEmpty;
    lodash.isEqual = isEqual;
    lodash.isEqualWith = isEqualWith;
    lodash.isError = isError;
    lodash.isFinite = isFinite;
    lodash.isFunction = isFunction;
    lodash.isInteger = isInteger;
    lodash.isLength = isLength;
    lodash.isMap = isMap;
    lodash.isMatch = isMatch;
    lodash.isMatchWith = isMatchWith;
    lodash.isNaN = isNaN;
    lodash.isNative = isNative;
    lodash.isNil = isNil;
    lodash.isNull = isNull;
    lodash.isNumber = isNumber;
    lodash.isObject = isObject;
    lodash.isObjectLike = isObjectLike;
    lodash.isPlainObject = isPlainObject;
    lodash.isRegExp = isRegExp;
    lodash.isSafeInteger = isSafeInteger;
    lodash.isSet = isSet;
    lodash.isString = isString;
    lodash.isSymbol = isSymbol;
    lodash.isTypedArray = isTypedArray;
    lodash.isUndefined = isUndefined;
    lodash.isWeakMap = isWeakMap;
    lodash.isWeakSet = isWeakSet;
    lodash.join = join;
    lodash.kebabCase = kebabCase;
    lodash.last = last;
    lodash.lastIndexOf = lastIndexOf;
    lodash.lowerCase = lowerCase;
    lodash.lowerFirst = lowerFirst;
    lodash.lt = lt;
    lodash.lte = lte;
    lodash.max = max;
    lodash.maxBy = maxBy;
    lodash.mean = mean;
    lodash.meanBy = meanBy;
    lodash.min = min;
    lodash.minBy = minBy;
    lodash.stubArray = stubArray;
    lodash.stubFalse = stubFalse;
    lodash.stubObject = stubObject;
    lodash.stubString = stubString;
    lodash.stubTrue = stubTrue;
    lodash.multiply = multiply;
    lodash.nth = nth;
    lodash.noConflict = noConflict;
    lodash.noop = noop;
    lodash.now = now;
    lodash.pad = pad;
    lodash.padEnd = padEnd;
    lodash.padStart = padStart;
    lodash.parseInt = parseInt;
    lodash.random = random;
    lodash.reduce = reduce;
    lodash.reduceRight = reduceRight;
    lodash.repeat = repeat;
    lodash.replace = replace;
    lodash.result = result;
    lodash.round = round;
    lodash.runInContext = runInContext;
    lodash.sample = sample;
    lodash.size = size;
    lodash.snakeCase = snakeCase;
    lodash.some = some;
    lodash.sortedIndex = sortedIndex;
    lodash.sortedIndexBy = sortedIndexBy;
    lodash.sortedIndexOf = sortedIndexOf;
    lodash.sortedLastIndex = sortedLastIndex;
    lodash.sortedLastIndexBy = sortedLastIndexBy;
    lodash.sortedLastIndexOf = sortedLastIndexOf;
    lodash.startCase = startCase;
    lodash.startsWith = startsWith;
    lodash.subtract = subtract;
    lodash.sum = sum;
    lodash.sumBy = sumBy;
    lodash.template = template;
    lodash.times = times;
    lodash.toFinite = toFinite;
    lodash.toInteger = toInteger;
    lodash.toLength = toLength;
    lodash.toLower = toLower;
    lodash.toNumber = toNumber;
    lodash.toSafeInteger = toSafeInteger;
    lodash.toString = toString;
    lodash.toUpper = toUpper;
    lodash.trim = trim;
    lodash.trimEnd = trimEnd;
    lodash.trimStart = trimStart;
    lodash.truncate = truncate;
    lodash.unescape = unescape;
    lodash.uniqueId = uniqueId;
    lodash.upperCase = upperCase;
    lodash.upperFirst = upperFirst;

    // Add aliases.
    lodash.each = forEach;
    lodash.eachRight = forEachRight;
    lodash.first = head;

    mixin(lodash, (function() {
      var source = {};
      baseForOwn(lodash, function(func, methodName) {
        if (!hasOwnProperty.call(lodash.prototype, methodName)) {
          source[methodName] = func;
        }
      });
      return source;
    }()), { 'chain': false });

    /*------------------------------------------------------------------------*/

    /**
     * The semantic version number.
     *
     * @static
     * @memberOf _
     * @type {string}
     */
    lodash.VERSION = VERSION;

    // Assign default placeholders.
    arrayEach(['bind', 'bindKey', 'curry', 'curryRight', 'partial', 'partialRight'], function(methodName) {
      lodash[methodName].placeholder = lodash;
    });

    // Add `LazyWrapper` methods for `_.drop` and `_.take` variants.
    arrayEach(['drop', 'take'], function(methodName, index) {
      LazyWrapper.prototype[methodName] = function(n) {
        n = n === undefined ? 1 : nativeMax(toInteger(n), 0);

        var result = (this.__filtered__ && !index)
          ? new LazyWrapper(this)
          : this.clone();

        if (result.__filtered__) {
          result.__takeCount__ = nativeMin(n, result.__takeCount__);
        } else {
          result.__views__.push({
            'size': nativeMin(n, MAX_ARRAY_LENGTH),
            'type': methodName + (result.__dir__ < 0 ? 'Right' : '')
          });
        }
        return result;
      };

      LazyWrapper.prototype[methodName + 'Right'] = function(n) {
        return this.reverse()[methodName](n).reverse();
      };
    });

    // Add `LazyWrapper` methods that accept an `iteratee` value.
    arrayEach(['filter', 'map', 'takeWhile'], function(methodName, index) {
      var type = index + 1,
          isFilter = type == LAZY_FILTER_FLAG || type == LAZY_WHILE_FLAG;

      LazyWrapper.prototype[methodName] = function(iteratee) {
        var result = this.clone();
        result.__iteratees__.push({
          'iteratee': getIteratee(iteratee, 3),
          'type': type
        });
        result.__filtered__ = result.__filtered__ || isFilter;
        return result;
      };
    });

    // Add `LazyWrapper` methods for `_.head` and `_.last`.
    arrayEach(['head', 'last'], function(methodName, index) {
      var takeName = 'take' + (index ? 'Right' : '');

      LazyWrapper.prototype[methodName] = function() {
        return this[takeName](1).value()[0];
      };
    });

    // Add `LazyWrapper` methods for `_.initial` and `_.tail`.
    arrayEach(['initial', 'tail'], function(methodName, index) {
      var dropName = 'drop' + (index ? '' : 'Right');

      LazyWrapper.prototype[methodName] = function() {
        return this.__filtered__ ? new LazyWrapper(this) : this[dropName](1);
      };
    });

    LazyWrapper.prototype.compact = function() {
      return this.filter(identity);
    };

    LazyWrapper.prototype.find = function(predicate) {
      return this.filter(predicate).head();
    };

    LazyWrapper.prototype.findLast = function(predicate) {
      return this.reverse().find(predicate);
    };

    LazyWrapper.prototype.invokeMap = baseRest(function(path, args) {
      if (typeof path == 'function') {
        return new LazyWrapper(this);
      }
      return this.map(function(value) {
        return baseInvoke(value, path, args);
      });
    });

    LazyWrapper.prototype.reject = function(predicate) {
      return this.filter(negate(getIteratee(predicate)));
    };

    LazyWrapper.prototype.slice = function(start, end) {
      start = toInteger(start);

      var result = this;
      if (result.__filtered__ && (start > 0 || end < 0)) {
        return new LazyWrapper(result);
      }
      if (start < 0) {
        result = result.takeRight(-start);
      } else if (start) {
        result = result.drop(start);
      }
      if (end !== undefined) {
        end = toInteger(end);
        result = end < 0 ? result.dropRight(-end) : result.take(end - start);
      }
      return result;
    };

    LazyWrapper.prototype.takeRightWhile = function(predicate) {
      return this.reverse().takeWhile(predicate).reverse();
    };

    LazyWrapper.prototype.toArray = function() {
      return this.take(MAX_ARRAY_LENGTH);
    };

    // Add `LazyWrapper` methods to `lodash.prototype`.
    baseForOwn(LazyWrapper.prototype, function(func, methodName) {
      var checkIteratee = /^(?:filter|find|map|reject)|While$/.test(methodName),
          isTaker = /^(?:head|last)$/.test(methodName),
          lodashFunc = lodash[isTaker ? ('take' + (methodName == 'last' ? 'Right' : '')) : methodName],
          retUnwrapped = isTaker || /^find/.test(methodName);

      if (!lodashFunc) {
        return;
      }
      lodash.prototype[methodName] = function() {
        var value = this.__wrapped__,
            args = isTaker ? [1] : arguments,
            isLazy = value instanceof LazyWrapper,
            iteratee = args[0],
            useLazy = isLazy || isArray(value);

        var interceptor = function(value) {
          var result = lodashFunc.apply(lodash, arrayPush([value], args));
          return (isTaker && chainAll) ? result[0] : result;
        };

        if (useLazy && checkIteratee && typeof iteratee == 'function' && iteratee.length != 1) {
          // Avoid lazy use if the iteratee has a "length" value other than `1`.
          isLazy = useLazy = false;
        }
        var chainAll = this.__chain__,
            isHybrid = !!this.__actions__.length,
            isUnwrapped = retUnwrapped && !chainAll,
            onlyLazy = isLazy && !isHybrid;

        if (!retUnwrapped && useLazy) {
          value = onlyLazy ? value : new LazyWrapper(this);
          var result = func.apply(value, args);
          result.__actions__.push({ 'func': thru, 'args': [interceptor], 'thisArg': undefined });
          return new LodashWrapper(result, chainAll);
        }
        if (isUnwrapped && onlyLazy) {
          return func.apply(this, args);
        }
        result = this.thru(interceptor);
        return isUnwrapped ? (isTaker ? result.value()[0] : result.value()) : result;
      };
    });

    // Add `Array` methods to `lodash.prototype`.
    arrayEach(['pop', 'push', 'shift', 'sort', 'splice', 'unshift'], function(methodName) {
      var func = arrayProto[methodName],
          chainName = /^(?:push|sort|unshift)$/.test(methodName) ? 'tap' : 'thru',
          retUnwrapped = /^(?:pop|shift)$/.test(methodName);

      lodash.prototype[methodName] = function() {
        var args = arguments;
        if (retUnwrapped && !this.__chain__) {
          var value = this.value();
          return func.apply(isArray(value) ? value : [], args);
        }
        return this[chainName](function(value) {
          return func.apply(isArray(value) ? value : [], args);
        });
      };
    });

    // Map minified method names to their real names.
    baseForOwn(LazyWrapper.prototype, function(func, methodName) {
      var lodashFunc = lodash[methodName];
      if (lodashFunc) {
        var key = lodashFunc.name + '';
        if (!hasOwnProperty.call(realNames, key)) {
          realNames[key] = [];
        }
        realNames[key].push({ 'name': methodName, 'func': lodashFunc });
      }
    });

    realNames[createHybrid(undefined, WRAP_BIND_KEY_FLAG).name] = [{
      'name': 'wrapper',
      'func': undefined
    }];

    // Add methods to `LazyWrapper`.
    LazyWrapper.prototype.clone = lazyClone;
    LazyWrapper.prototype.reverse = lazyReverse;
    LazyWrapper.prototype.value = lazyValue;

    // Add chain sequence methods to the `lodash` wrapper.
    lodash.prototype.at = wrapperAt;
    lodash.prototype.chain = wrapperChain;
    lodash.prototype.commit = wrapperCommit;
    lodash.prototype.next = wrapperNext;
    lodash.prototype.plant = wrapperPlant;
    lodash.prototype.reverse = wrapperReverse;
    lodash.prototype.toJSON = lodash.prototype.valueOf = lodash.prototype.value = wrapperValue;

    // Add lazy aliases.
    lodash.prototype.first = lodash.prototype.head;

    if (symIterator) {
      lodash.prototype[symIterator] = wrapperToIterator;
    }
    return lodash;
  });

  /*--------------------------------------------------------------------------*/

  // Export lodash.
  var _ = runInContext();

  // Some AMD build optimizers, like r.js, check for condition patterns like:
  if (true) {
    // Expose Lodash on the global object to prevent errors when Lodash is
    // loaded by a script tag in the presence of an AMD loader.
    // See http://requirejs.org/docs/errors.html#mismatch for more details.
    // Use `_.noConflict` to remove Lodash from the global object.
    root._ = _;

    // Define as an anonymous module so, through path mapping, it can be
    // referenced as the "underscore" module.
    !(__WEBPACK_AMD_DEFINE_RESULT__ = (function() {
      return _;
    }).call(exports, __webpack_require__, exports, module),
		__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
  }
  // Check for `exports` after `define` in case a build optimizer adds it.
  else {}
}.call(this));


/***/ }),

/***/ "./resources/css/app.css":
/*!*******************************!*\
  !*** ./resources/css/app.css ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./node_modules/process/browser.js":
/*!*****************************************!*\
  !*** ./node_modules/process/browser.js ***!
  \*****************************************/
/***/ ((module) => {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			id: moduleId,
/******/ 			loaded: false,
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					result = fn();
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/global */
/******/ 	(() => {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/node module decorator */
/******/ 	(() => {
/******/ 		__webpack_require__.nmd = (module) => {
/******/ 			module.paths = [];
/******/ 			if (!module.children) module.children = [];
/******/ 			return module;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/js/app": 0,
/******/ 			"css/app": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			for(moduleId in moreModules) {
/******/ 				if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 					__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 				}
/******/ 			}
/******/ 			if(runtime) var result = runtime(__webpack_require__);
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkIds[i]] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunk"] = self["webpackChunk"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["css/app"], () => (__webpack_require__("./resources/js/app.js")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["css/app"], () => (__webpack_require__("./resources/css/app.css")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;