$ErrorActionPreference = 'Stop'

$projectRoot = (Resolve-Path -LiteralPath (Join-Path $PSScriptRoot '..')).Path
$distRoot = Join-Path $projectRoot 'dist'
$packageRoot = Join-Path $distRoot '.last_version'
$archivePath = Join-Path $distRoot '.last_version.zip'

if ([IO.Path]::GetFileName($projectRoot) -ne 'ss.errorloger') {
  throw "Unexpected project root: $projectRoot"
}

if (Test-Path -LiteralPath $distRoot) {
  Remove-Item -LiteralPath $distRoot -Recurse -Force
}
New-Item -ItemType Directory -Path $packageRoot | Out-Null

$runtimeEntries = @('admin', 'install', 'lang', 'lib', 'views', 'include.php', 'LICENSE')
foreach ($entry in $runtimeEntries) {
  $source = Join-Path $projectRoot $entry
  if (-not (Test-Path -LiteralPath $source)) {
    throw "Required Marketplace entry is missing: $entry"
  }
  Copy-Item -LiteralPath $source -Destination $packageRoot -Recurse
}

Add-Type -AssemblyName System.IO.Compression
$archiveStream = [IO.File]::Open($archivePath, [IO.FileMode]::CreateNew)
try {
  $archive = [IO.Compression.ZipArchive]::new(
    $archiveStream,
    [IO.Compression.ZipArchiveMode]::Create,
    $false
  )
  try {
    Get-ChildItem -LiteralPath $packageRoot -Recurse -File | ForEach-Object {
      $relativePath = $_.FullName.Substring($packageRoot.Length).TrimStart([char[]]@('\', '/'))
      $entryName = '.last_version/' + $relativePath.Replace('\', '/')
      $entry = $archive.CreateEntry($entryName, [IO.Compression.CompressionLevel]::Optimal)
      $entryStream = $entry.Open()
      $sourceStream = [IO.File]::OpenRead($_.FullName)
      try {
        $sourceStream.CopyTo($entryStream)
      } finally {
        $sourceStream.Dispose()
        $entryStream.Dispose()
      }
    }
  } finally {
    $archive.Dispose()
  }
} finally {
  $archiveStream.Dispose()
}
Write-Host "Marketplace archive: $archivePath"
