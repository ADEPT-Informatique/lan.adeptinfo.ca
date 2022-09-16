using api_adept.Context;
using api_adept.Core;
using api_adept.Services;
using FirebaseAdmin;
using FirebaseAdmin.Auth;
using Google.Apis.Auth.OAuth2;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.EntityFrameworkCore;
using Microsoft.IdentityModel.Tokens;

var builder = WebApplication.CreateBuilder(args);

// Add services to the container.
builder.Services.AddControllers();
// Learn more about configuring Swagger/OpenAPI at https://aka.ms/aspnetcore/swashbuckle
builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen();




// Add Database
builder.Services.AddDbContext<AdeptLanContext>(options =>
{
    options.UseSqlServer(builder.Configuration.GetConnectionString("AdeptLanDatabase"));
});

AddAdeptServices(builder.Services);
AddAdeptAuth(builder.Services);


var app = builder.Build();

// Configure the HTTP request pipeline.
if (app.Environment.IsDevelopment())
{
    app.UseSwagger();
    app.UseSwaggerUI();
}

app.UseHttpsRedirection();

app.UseAuthentication();
app.UseAuthorization();

app.UseMiddleware<ExceptionMiddleware>();

app.MapControllers();

app.Run();




void AddAdeptServices(IServiceCollection services)
{
    services.AddTransient<IUsersService, UsersService>();
}

void AddAdeptAuth(IServiceCollection services)
{
    var defaultApp = FirebaseApp.Create(new AppOptions()
    {
        Credential = GoogleCredential.FromFile("./service-account-file.json")
    });

    var defaultAuth = FirebaseAuth.GetAuth(defaultApp);


    builder.Services.AddAuthentication(JwtBearerDefaults.AuthenticationScheme)
    .AddJwtBearer(opt =>
    {
        opt.Authority = "https://securetoken.google.com/lan-adept";
        opt.TokenValidationParameters = new TokenValidationParameters
        {
            ValidateIssuer = true,
            ValidateAudience = true,
            ValidateLifetime = true,
            ValidateIssuerSigningKey = true,
            ValidIssuer = "https://securetoken.google.com/lan-adept",
            ValidAudience = "lan-adept"
        };
    });
    //builder.Services.AddTransient<IFirebaseAuthAdapter>(provider =>
    //{
    //    var googleCredential = GoogleCredential.FromFile("firebase-auth.json");
    //    return new FirebaseAuthAdapter(googleCredential, "adept-api");
    //});
    //builder.Services.AddTransient<IFirebaseAuthManager, FirebaseAuthManager>();
}