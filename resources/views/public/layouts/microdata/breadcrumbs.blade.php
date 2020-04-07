<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
  @foreach ($breadcrumbs as $i => $breadcrumb)
    @if(!$breadcrumb->last)
      {{ $i>0?',':'' }}
      {
        "@type": "ListItem",
        "position": {{ $i+1 }},
        "item":
        {
          "@id": "{{ $breadcrumb->url }}",
          "name": "{{ $breadcrumb->title }}"
        }
      }
    @endif
  @endforeach
  ]
}
</script>