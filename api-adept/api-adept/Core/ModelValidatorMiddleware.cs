﻿using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.ModelBinding;
using Newtonsoft.Json;

namespace ADEPT_API.LIBRARY.Middleware
{
  public static class ModelValidatorMiddleware
  {
    public static IActionResult ValidateModelState(ActionContext context)
    {
      (string fieldName, ModelStateEntry errorEntry) = context.ModelState.FirstOrDefault(kvp => kvp.Value.Errors.Count > 0);

      var serializedErrorMessage = errorEntry.Errors.First().ErrorMessage;
      var badRequestObject = JsonConvert.DeserializeObject(serializedErrorMessage);

      return new BadRequestObjectResult(badRequestObject);
    }
  }
}
