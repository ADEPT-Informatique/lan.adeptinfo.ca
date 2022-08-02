using FirebaseAdmin;
using FirebaseAdmin.Auth;
using Google.Apis.Auth.OAuth2;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.DependencyInjection;

var builder = WebApplication.CreateBuilder(args);
var configuration = builder.Configuration;

builder.Services.AddDbContext<AdeptContext>(options =>
    options.UseSqlServer(configuration.GetConnectionString("AdeptContext") 
    ?? throw new InvalidOperationException("Connection string 'AdeptContext' not found.")));

// Add services to the container.

builder.Services.AddControllers();
// Learn more about configuring Swagger/OpenAPI at https://aka.ms/aspnetcore/swashbuckle
builder.Services.AddEndpointsApiExplorer();
builder.Services.AddSwaggerGen();

var app = builder.Build();

var defaultApp = FirebaseApp.Create(new AppOptions()
{
    Credential = GoogleCredential.FromFile("../service-account-file.json"),
});

var defaultAuth = FirebaseAuth.GetAuth(defaultApp);

// Configure the HTTP request pipeline.
if (app.Environment.IsDevelopment())
{
    app.UseSwagger();
    app.UseSwaggerUI();
}

app.UseHttpsRedirection();

app.UseAuthentication();
app.UseAuthorization();

app.MapControllers();


app.Run();
